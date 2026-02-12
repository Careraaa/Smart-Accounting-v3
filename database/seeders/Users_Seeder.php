<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed users table
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'email_verified_at' => '2026-02-06 12:11:11',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',
                'role' => 'hr',
                'profile_picture' => null,
                'remember_token' => 'PD8ffT4qLQ',
                'created_at' => '2026-02-06 12:11:11',
                'updated_at' => '2026-02-06 12:11:11',
            ],
            [
                'id' => 2,
                'name' => 'Remittance Clerk',
                'email' => 'remittance@example.com',
                'email_verified_at' => '2026-02-06 12:11:11',
                'password' => '$2y$12$/La9Q6JGZkIbM1G5Ah.dwOAqcGhBJbU94pUeyauuq0jgZx4Hj/tr6',
                'role' => 'remittance_clerk',
                'profile_picture' => null,
                'remember_token' => '26TRckTpBv',
                'created_at' => '2026-02-06 12:11:11',
                'updated_at' => '2026-02-06 12:11:11',
            ],
            [
                'id' => 3,
                'name' => 'Accountant',
                'email' => 'accountant@example.com',
                'email_verified_at' => '2026-02-06 12:11:12',
                'password' => '$2y$12$eM2JIPsh.y4LVUlUv.xv6.VC.156R3pPLUAphTEKeFXINlqGIA5nm',
                'role' => 'accountant',
                'profile_picture' => null,
                'remember_token' => 'K0ve6RLqfM',
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
        ]);
    }
}
