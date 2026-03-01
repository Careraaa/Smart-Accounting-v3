<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed merged users table (includes employee data)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', // password: password
                'role' => 'superadmin',
                'profile_picture' => null,
                'remember_token' => 'SuperAdminToken01',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'phone' => null,
                'address' => null,
                'date_of_birth' => null,
                'date_of_hire' => null,
                'position' => null,
                'department' => null,
                'status' => 'active',
                'salary_rate' => 0,
                'created_at' => '2026-02-06 12:11:11',
                'updated_at' => '2026-02-06 12:11:11',
            ],
            [
                'id' => 2,
                'name' => 'HR Manager',
                'username' => 'hrmanager',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS',
                'role' => 'hr',
                'profile_picture' => null,
                'remember_token' => 'PD8ffT4qLQ',
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
                'created_at' => '2026-02-06 12:11:11',
                'updated_at' => '2026-02-06 12:11:11',
            ],
            [
                'id' => 3,
                'name' => 'Remittance Clerk',
                'username' => 'remittance_clerk',
                'password' => '$2y$12$/La9Q6JGZkIbM1G5Ah.dwOAqcGhBJbU94pUeyauuq0jgZx4Hj/tr6',
                'role' => 'remittance_clerk',
                'profile_picture' => null,
                'remember_token' => '26TRckTpBv',
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
                'created_at' => '2026-02-06 12:11:11',
                'updated_at' => '2026-02-06 12:11:11',
            ],
            [
                'id' => 4,
                'name' => 'Accountant',
                'username' => 'accountant',
                'password' => '$2y$12$eM2JIPsh.y4LVUlUv.xv6.VC.156R3pPLUAphTEKeFXINlqGIA5nm',
                'role' => 'accountant',
                'profile_picture' => null,
                'remember_token' => 'K0ve6RLqfM',
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
                'id' => 5,
                'name' => 'John Doe',
                'username' => 'johndoe',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', // password: password
                'role' => 'employee',
                'profile_picture' => null,
                'remember_token' => 'XxYyZzDemo1',
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
                'id' => 6,
                'name' => 'Angela Fernandez',
                'username' => 'angelafernandez',
                'password' => '$2y$12$YhTTF5BRZfCgAA9.G1RFyObHHru/l1suzFQ2cXZvWvYYze66mQ/hS', // password: password
                'role' => 'employee',
                'profile_picture' => null,
                'remember_token' => 'AngelaDemo123',
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
                'created_at' => '2026-02-26 02:09:57',
                'updated_at' => '2026-02-26 02:09:57',
            ],
        ]);
    }
}

