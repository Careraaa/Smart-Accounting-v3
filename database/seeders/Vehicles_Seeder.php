<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Vehicles_Seeder extends Seeder
{
    public function run(): void
    {
        // Seed vehicles table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('vehicles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        DB::table('vehicles')->insert([
            [
                'plate_number' => 'ABC-123',
                'vehicle_type' => null,
                'make' => null,
                'model' => null,
                'year' => null,
                'capacity' => null,
                'status' => 'active',
                'date_purchased' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_number' => 'XYZ-789',
                'vehicle_type' => null,
                'make' => null,
                'model' => null,
                'year' => null,
                'capacity' => null,
                'status' => 'active',
                'date_purchased' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
