<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([Users_Seeder::class]);
        $this->call([StatutoryDeductions_Seeder::class]);
        $this->call([Drivers_Seeder::class]);
        $this->call([Vehicles_Seeder::class]);
        $this->call([Pao_Seeder::class]);
        $this->call([Employees_Seeder::class]);
        // Disable foreign key checks for seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ===== DEFAULT PASSWORDS =====
        // All accounts use password: "password"
        // ==============================

        // Seed users table
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'email_verified_at' => '2026-02-06 12:11:11',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', // password: password
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
                'password' => '$2y$12$/La9Q6JGZkIbM1G5Ah.dwOAqcGhBJbU94pUeyauuq0jgZx4Hj/tr6', // password: password
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
                'password' => '$2y$12$eM2JIPsh.y4LVUlUv.xv6.VC.156R3pPLUAphTEKeFXINlqGIA5nm', // password: password
                'role' => 'accountant',
                'profile_picture' => null,
                'remember_token' => 'K0ve6RLqfM',
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
            [
                'id' => 4,
                'name' => 'John Doe',
                'email' => 'employee@example.com',
                'email_verified_at' => '2026-02-06 12:11:12',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', // password: password
                'role' => 'employee',
                'profile_picture' => null,
                'remember_token' => 'XxYyZzDemo1',
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
        ]);

        // Seed employees table
        DB::table('employees')->truncate();
        DB::table('employees')->insert([
            [
                'id' => 1,
                'user_id' => 4,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'employee@example.com',
                'phone' => '09123456789',
                'address' => '123 Main Street, City, Philippines',
                'date_of_birth' => '1995-05-15',
                'date_of_hire' => '2024-01-15',
                'position' => 'Driver',
                'department' => 'Operations',
                'status' => 'active',
                'salary_rate' => 800.00,
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
