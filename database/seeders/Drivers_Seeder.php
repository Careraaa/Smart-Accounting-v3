<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Drivers_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed drivers table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('drivers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        DB::table('drivers')->insert([
            [
                'name' => 'Juan Dela Cruz',
                'license_number' => 'DLN-2024-001',
                'contact_number' => '09171234567',
                'email' => 'juan.delacruz@example.com',
                'address' => '123 Main Street, Metro Manila',
                'date_of_hire' => '2024-01-15',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria Santos',
                'license_number' => 'DLN-2024-002',
                'contact_number' => '09187654321',
                'email' => 'maria.santos@example.com',
                'address' => '456 Oak Avenue, Quezon City',
                'date_of_hire' => '2024-02-20',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
