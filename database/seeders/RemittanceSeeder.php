<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\DailyRemittance;
use Carbon\Carbon;

class RemittanceSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------
        // DRIVERS
        // -------------------------
        $driver1 = Driver::updateOrCreate(
            ['license_number' => 'N01-23-123456'],
            [
                'name' => 'Juan Dela Cruz',
                'contact_number' => '09171234567',
                'gender' => 'Male',
                'email' => 'juan@example.com',
                'address' => '123 Rizal St., Brgy. Commonwealth, Quezon City, Metro Manila',
                'date_of_hire' => now()->subYears(3),
                'status' => 'active',
            ]
        );

        $driver2 = Driver::updateOrCreate(
            ['license_number' => 'N02-45-654321'],
            [
                'name' => 'Pedro Santos',
                'contact_number' => '09987654321',
                'gender' => 'Male',
                'email' => 'pedro@example.com',
                'address' => '45 Bonifacio Ave., Brgy. 289, City of Manila, Metro Manila',
                'date_of_hire' => now()->subYears(2),
                'status' => 'active',
            ]
        );

        // -------------------------
        // PAOs
        // -------------------------
        $pao1 = PAO::updateOrCreate(
            ['email' => 'maria@example.com'],
            [
                'name' => 'Maria Lopez',
                'contact_number' => '09170000000',
                'gender' => 'Female',
                'address' => '88 C. Raymundo Ave., Brgy. Rosario, Pasig City, Metro Manila',
                'date_of_hire' => now()->subYears(4),
                'status' => 'active',
            ]
        );

        $pao2 = PAO::updateOrCreate(
            ['email' => 'ana@example.com'],
            [
                'name' => 'Ana Reyes',
                'contact_number' => '09171111111',
                'gender' => 'Female',
                'address' => '210 Kalayaan Ave., Brgy. Poblacion, Makati City, Metro Manila',
                'date_of_hire' => now()->subYears(1),
                'status' => 'active',
            ]
        );

        // -------------------------
        // ROUTES (FIXED: no route_code)
        // -------------------------
        $route1 = Route::updateOrCreate(
            ['route_name' => 'Cubao to Fairview'],
            [
                'origin' => 'Cubao',
                'destination' => 'Fairview',
                'boundary' => 1500,
                'status' => 'active',
            ]
        );

        $route2 = Route::updateOrCreate(
            ['route_name' => 'Divisoria to Baclaran'],
            [
                'origin' => 'Divisoria',
                'destination' => 'Baclaran',
                'boundary' => 1800,
                'status' => 'active',
            ]
        );

        // -------------------------
        // VEHICLES (no hardcoded IDs)
        // -------------------------
        $vehicle1 = Vehicle::updateOrCreate(
            ['plate_number' => 'ABC-1234'],
            [
                'route_id' => $route1->id,
                'operator' => 'Operator A',
                'status' => 'active',
            ]
        );

        $vehicle2 = Vehicle::updateOrCreate(
            ['plate_number' => 'XYZ-5678'],
            [
                'route_id' => $route2->id,
                'operator' => 'Operator B',
                'status' => 'active',
            ]
        );
    }
}