<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'employee1@omx.com')->first();
        if (! $user) return;

        $late_minutes_options = [2, 7, 9, 30]; // possible late minutes

        for ($i = 0; $i < 50; $i++) {
            // Random day in the past 50 days
            $attendance_date = Carbon::today()->subDays($i);
            // Decide if the day is on time or late
            $is_late = false;

            $punctual_time = $attendance_date->copy()->setTime(10, 0, 0); // 10:00 AM
            if ($is_late) {
                $minutes_late = $late_minutes_options[array_rand($late_minutes_options)];
                $check_in_time = $punctual_time->copy()->addMinutes($minutes_late);
            } else {
                $check_in_time = $punctual_time->copy()->subMinutes(rand(0, 5)); // early by 0-5 mins
                $minutes_late = null;
            }

            // Check-out: assume 8 hours work
            $check_out_time = $check_in_time->copy()->addHours(8)->addMinutes(rand(0, 15));

            Attendance::create([
                'user_id'      => $user->id,
                'date'         => $attendance_date,
                'check_in'     => $check_in_time,
                'check_out'    => $check_out_time,
                'punctual'     => ! $is_late,
                'minutes_late' => $minutes_late,
            ]);
        }
    }
}
