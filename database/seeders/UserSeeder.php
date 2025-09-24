<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Seeder
        $user = User::create([
            'name'       => 'OMX Admin',
            'email'      => 'admin@omx.com',
            'password'   => Hash::make('password'),
        ]);
        $user->assignRole('admin');

        // Employee Seeder
        $employee_names = [
            'Amit Sharma',
            'Priya Singh',
            'Rohan Gupta',
            'Neha Patel',
            'Vikram Rao',
            'Anjali Mehta',
            'Siddharth Jain',
            'Pooja Verma',
            'Rahul Khanna',
            'Sanya Kapoor'
        ];

        foreach ($employee_names as $index => $name) {
            $email = 'employee' . ($index + 1) . '@omx.com';

            $employee = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                ]
            );
            $employee->assignRole('employee');
        }
    }
}
