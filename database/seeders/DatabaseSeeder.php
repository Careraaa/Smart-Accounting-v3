<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users for each role
        User::factory()->create([
            'name' => 'HR Manager',
            'email' => 'hr@example.com',
            'password' => bcrypt('password123'),
            'role' => 'hr',
        ]);

        User::factory()->create([
            'name' => 'Remittance Clerk',
            'email' => 'remittance@example.com',
            'password' => bcrypt('password123'),
            'role' => 'remittance_clerk',
        ]);

        User::factory()->create([
            'name' => 'Accountant',
            'email' => 'accountant@example.com',
            'password' => bcrypt('password123'),
            'role' => 'accountant',
        ]);
    }
}
