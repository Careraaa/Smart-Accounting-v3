<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Employees_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed employees table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('employees')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        DB::table('employees')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'first_name' => 'HR',
                'last_name' => 'Manager',
                'email' => 'hr@example.com',
                'phone' => '09101234567',
                'address' => '100 Admin Street, City, Philippines',
                'date_of_birth' => '1985-02-10',
                'date_of_hire' => '2020-01-01',
                'position' => 'HR Manager',
                'department' => 'Human Resources',
                'status' => 'active',
                'salary_rate' => 1000.00,
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'first_name' => 'Remittance',
                'last_name' => 'Clerk',
                'email' => 'remittance@example.com',
                'phone' => '09111234567',
                'address' => '200 Clerk Street, City, Philippines',
                'date_of_birth' => '1993-06-15',
                'date_of_hire' => '2021-03-15',
                'position' => 'Remittance Clerk',
                'department' => 'Finance',
                'status' => 'active',
                'salary_rate' => 700.00,
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'first_name' => 'Account',
                'last_name' => 'Ant',
                'email' => 'accountant@example.com',
                'phone' => '09121234567',
                'address' => '300 Accounting Street, City, Philippines',
                'date_of_birth' => '1990-09-20',
                'date_of_hire' => '2020-06-01',
                'position' => 'Accountant',
                'department' => 'Finance',
                'status' => 'active',
                'salary_rate' => 950.00,
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'employee@example.com',
                'phone' => '09123456789',
                'address' => '123 Main Street, City, Philippines',
                'date_of_birth' => '1995-05-15',
                'date_of_hire' => '2024-01-15',
                'position' => 'Staff',
                'department' => 'IT',
                'status' => 'active',
                'salary_rate' => 800.00,
                'created_at' => '2026-02-06 12:11:12',
                'updated_at' => '2026-02-06 12:11:12',
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'first_name' => 'Angela',
                'last_name' => 'Fernandez',
                'email' => 'angela.fernandez@example.com',
                'phone' => '09192345678',
                'address' => '666 Birch Road, Davao City',
                'date_of_birth' => '1992-08-15',
                'date_of_hire' => '2023-07-15',
                'position' => 'Finance Officer',
                'department' => 'Finance',
                'status' => 'active',
                'salary_rate' => 650.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
