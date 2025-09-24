<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tasks;
use App\Models\Attendance;
use App\Models\Grade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $user   = User::findOrFail($userId);

        $metrics = $this->calculateMetrics($user);

        // prepare grade snapshot data
        $gradeData = [
            'user_id'                => $userId,
            'task_performance_total' => $metrics['task_performance_total'],
            'attendance_points'      => $metrics['attendance_points'],
            'final_score'            => $metrics['final_score'],
            'grade'                  => $metrics['grade'],
            'calculated_at'          => Carbon::now(),
        ];

        // update today's grade row if exists, otherwise create a new one
        $existing = Grade::where('user_id', $userId)
            ->whereDate('calculated_at', Carbon::today())
            ->first();

        if ($existing) {
            $existing->update($gradeData);
            $gradeRecord = $existing;
        } else {
            $gradeRecord = Grade::create($gradeData);
        }

        // pass gradeRecord to view in case you want to show last saved snapshot
        return view('dashboard', compact('user', 'metrics', 'gradeRecord'));
    }

    /**
     * API          : return scoring & grade for the user
     * Route example: GET /api/user/{user}/grade
     */
    public function gradeApi(String $userId)
    {
        $user    = User::findOrFail($userId);
        $metrics = $this->calculateMetrics($user);

        return response()->json([
            'user_id'          => $user->id,
            'name'             => $user->name ?? $user->full_name ?? null,
            'task_performance' => [
                'assigned'               => (int)$metrics['assigned_count'],
                'completed'              => (int)$metrics['completed_count'],
                'completion_rate_points' => round($metrics['completion_rate_points'], 2),
                'on_time_points'         => round($metrics['on_time_points'], 2),
                'penalties'              => round($metrics['penalties'], 2),
                'total'                  => round($metrics['task_performance_total'], 2),
            ],
            'attendance' => [
                'punctuality_percent' => round($metrics['punctuality_percent'], 2),
                'points'              => (int)$metrics['attendance_points'],
            ],
            'final_score' => round($metrics['final_score'], 2),
            'grade'       => $metrics['grade'],
        ]);
    }

    // calculate the matrices (grade, attendance and task performance)
    protected function calculateMetrics(User $user): array
    {
        $userId = $user->id;

        // --- Tasks aggregates ---
        $assigned = Tasks::where('user_id', $userId)->count();

        $completedCount = (int) Tasks::where('user_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereNotNull('completed_at');
            })->count();

        $onTimeCount = (int) Tasks::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereNotNull('due_date')
            ->whereColumn('completed_at', '<=', 'due_date')
            ->count();

        $completionRatePoints = $assigned === 0 ? 0.0 : ($completedCount / $assigned) * 50.0;
        $onTimePoints         = $assigned === 0 ? 0.0 : ($onTimeCount / $assigned) * 30.0;

        // --- Penalties ---
        $now          = Carbon::now();
        $delayedTasks = Tasks::where('user_id', $userId)
            ->where(function ($q) use ($now) {
                $q->whereNotNull('completed_at')
                    ->whereNotNull('due_date')
                    ->whereColumn('completed_at', '>', 'due_date')
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereNull('completed_at')
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', $now);
                    });
            })
            ->get(['due_date', 'completed_at']);

        $minorPenaltyCount  = 0;
        $severePenaltyCount = 0;

        foreach ($delayedTasks as $t) {
            $due         = $t->due_date ? Carbon::parse($t->due_date) : null;
            $completedAt = $t->completed_at ? Carbon::parse($t->completed_at) : null;

            if ($completedAt && $due) {
                $delayDays = $due->diffInDays($completedAt);
                if ($delayDays <= 2) $minorPenaltyCount++;
                else $severePenaltyCount++;
            } elseif (!$completedAt && $due && $now->gt($due)) {
                $severePenaltyCount++;
            }
        }

        // Penalty cap
        $minorPenalties  = max($minorPenaltyCount * -2, -10);   // cap at -10
        $severePenalties = max($severePenaltyCount * -5, -20);  // cap at -20
        $penalties = $minorPenalties + $severePenalties;

        $taskPerformanceTotal = $completionRatePoints + $onTimePoints + $penalties;
        $taskPerformanceTotal = max(0, min($taskPerformanceTotal, 80));

        // --- Attendance ---
        $totalAttendanceDays = Attendance::where('user_id', $userId)->count();
        $punctualCount       = Attendance::where('user_id', $userId)
            ->where('punctual', true)
            ->count();

        $punctualityPercent = $totalAttendanceDays ? ($punctualCount / $totalAttendanceDays) * 100.0 : 0.0;

        if ($punctualityPercent >= 90) $attendancePoints = 20;
        elseif ($punctualityPercent >= 75) $attendancePoints = 15;
        elseif ($punctualityPercent >= 60) $attendancePoints = 10;
        else   $attendancePoints                             = 0;

        $finalScore = $taskPerformanceTotal + $attendancePoints;
        $finalScore = min(100, $finalScore);

        $grade = $this->mapGrade([
            'final_score'             => $finalScore,
            'completion_rate_percent' => $assigned ? ($completedCount / $assigned) * 100.0 : 0.0,
            'severe_delay_count'      => $severePenaltyCount,
            'assigned_count'          => $assigned,
            'punctuality_percent'     => $punctualityPercent,
            'attendance_points'       => $attendancePoints,
            'minor_delay_count'       => $minorPenaltyCount,
        ]);

        return [
            'assigned_count'         => $assigned,
            'completed_count'        => $completedCount,
            'completion_rate_points' => $completionRatePoints,
            'on_time_points'         => $onTimePoints,
            'penalties'              => $penalties,
            'task_performance_total' => $taskPerformanceTotal,
            'punctuality_percent'    => $punctualityPercent,
            'attendance_points'      => $attendancePoints,
            'final_score'            => $finalScore,
            'grade'                  => $grade,
            'minor_delay_count'      => $minorPenaltyCount,
            'severe_delay_count'     => $severePenaltyCount,
        ];
    }


    /**
     * Map computed metrics to a grade (A/B/C/D).
     */
    protected function mapGrade(array $data): string
    {
        $score            = $data['final_score'];
        $completionRate   = $data['completion_rate_percent'];
        $severeDelays     = $data['severe_delay_count'];
        $assigned         = $data['assigned_count'] ?: 1;
        $minorDelays      = $data['minor_delay_count'] ?? 0;
        $attendancePoints = $data['attendance_points'];

        $delayedTasks   = $minorDelays + $severeDelays;
        $delayedPercent = ($delayedTasks / $assigned) * 100.0;

        // Grade A: score >=90, completion >=98%, no severe delays, attendancePoints >=15
        if ($score >= 90 && $completionRate >= 98 && $severeDelays === 0 && $attendancePoints >= 15) {
            return 'A';
        }

        // Grade B: 75 <= score < 90, delayedPercent <=5% and no severe delays, attendancePoints >=15, delays only minor
        if ($score >= 75 && $score < 90 && $delayedPercent <= 5 && $severeDelays === 0 && $attendancePoints >= 15 && $minorDelays === $delayedTasks) {
            return 'B';
        }

        // Grade C: 60 <= score < 75 OR delayed >5% OR >=1 severe delay
        if (($score >= 60 && $score < 75) || $delayedPercent > 5 || $severeDelays >= 1) {
            return 'C';
        }

        // Grade D: score < 60 or fallback
        return 'D';
    }
}
