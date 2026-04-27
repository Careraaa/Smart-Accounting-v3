<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Pao_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed paos table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('paos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        DB::table('paos')->insert([
            [
                'name' => 'Pedro Reyes',
                'contact_number' => '09151111111',
                'email' => 'pedro.reyes@example.com',
                'address' => '789 Pine Street, Brgy. Poblacion, Makati City, Metro Manila',
                'date_of_hire' => '2024-03-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rosa Garcia',
                'contact_number' => '09162222222',
                'email' => 'rosa.garcia@example.com',
                'address' => '321 Maple Drive, Brgy. San Antonio, Pasig City, Metro Manila',
                'date_of_hire' => '2024-03-15',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
