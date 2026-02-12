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
                'first_name' => 'Antonio',
                'last_name' => 'Morales',
                'email' => 'antonio.morales@example.com',
                'phone' => '09191234567',
                'address' => '555 Cedar Lane, Cebu City',
                'date_of_birth' => '1990-05-20',
                'date_of_hire' => '2023-06-01',
                'position' => 'Operations Manager',
                'department' => 'Operations',
                'status' => 'active',
                'salary_rate' => 650.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
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
