<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tasks;
use App\Enums\TaskStatus;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TasksSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'employee1@omx.com')->first();
        $faker = Faker::create();
        $task_status_class = TaskStatus::class;

        for ($i = 1; $i <= 50; $i++) {
            // distribute statuses
            $random = rand(1, 100);
            if ($random <= 55) {
                $status = $task_status_class::COMPLETED;
            } elseif ($random <= 75) {
                $status = $task_status_class::IN_PROCESS;
            } elseif ($random <= 90) {
                $status = $task_status_class::PENDING;
            } else {
                $status = $task_status_class::DELAYS;
            }

            $due_date = Carbon::now()->addDays(rand(-15, 15))->setTime(rand(8, 18), rand(0, 59), 0);

            $completed_at = null;

            if ($status === $task_status_class::COMPLETED) {
                // Completed tasks: decide on-time, minor delay (1-2 days), or severe delay (>2 days)
                $chance = rand(1, 100);
                if ($chance <= 70) {
                    $completed_at = (clone $due_date)->subDays(rand(0, 2))->addHours(rand(0, 12))->addMinutes(rand(0, 59));
                } elseif ($chance <= 90) {
                    $completed_at = (clone $due_date)->addDays(rand(1, 2))->addHours(rand(0, 12))->addMinutes(rand(0, 59));
                } else {
                    $completed_at = (clone $due_date)->addDays(rand(3, 10))->addHours(rand(0, 12))->addMinutes(rand(0, 59));
                }
            } elseif ($status === $task_status_class::DELAYS) {
                // Tasks explicitly marked as delayed: always past due and not completed
                $due_date = Carbon::now()->subDays(rand(3, 15));
                $completed_at = null;
            }

            Tasks::create([
                'user_id'      => $user->id,
                'title'        => ucfirst($faker->words(rand(3, 6), true)),
                'description'  => $faker->sentence(rand(8, 20)),
                'due_date'     => $due_date,
                'completed_at' => $completed_at,
                'status'       => $status,
            ]);
        }
    }
}
