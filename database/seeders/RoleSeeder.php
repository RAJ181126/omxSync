<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('roles')->insert([
            [
                'name'       => 'admin',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'employee',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
