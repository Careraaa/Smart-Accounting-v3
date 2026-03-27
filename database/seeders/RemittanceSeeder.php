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
        $driver1 = Driver::create([
            'name' => 'Juan Dela Cruz',
            'license_number' => 'N01-23-123456',
            'contact_number' => '09171234567',
            'gender' => 'Male',
            'email' => 'juan@example.com',
            'address' => 'Quezon City',
            'date_of_hire' => now()->subYears(3),
            'status' => 'active',
        ]);

        $driver2 = Driver::create([
            'name' => 'Pedro Santos',
            'license_number' => 'N02-45-654321',
            'contact_number' => '09987654321',
            'gender' => 'Male',
            'email' => 'pedro@example.com',
            'address' => 'Manila',
            'date_of_hire' => now()->subYears(2),
            'status' => 'active',
        ]);

        // -------------------------
        // PAOs
        // -------------------------
        $pao1 = PAO::create([
            'name' => 'Maria Lopez',
            'contact_number' => '09170000000',
            'gender' => 'Female',
            'email' => 'maria@example.com',
            'address' => 'Pasig',
            'date_of_hire' => now()->subYears(4),
            'status' => 'active',
        ]);

        $pao2 = PAO::create([
            'name' => 'Ana Reyes',
            'contact_number' => '09171111111',
            'gender' => 'Female',
            'email' => 'ana@example.com',
            'address' => 'Makati',
            'date_of_hire' => now()->subYears(1),
            'status' => 'active',
        ]);

        // -------------------------
        // ROUTES (FIXED: no route_code)
        // -------------------------
        $route1 = Route::create([
            'route_name' => 'Cubao to Fairview',
            'origin' => 'Cubao',
            'destination' => 'Fairview',
            'boundary' => 1500,
            'status' => 'active',
        ]);

        $route2 = Route::create([
            'route_name' => 'Divisoria to Baclaran',
            'origin' => 'Divisoria',
            'destination' => 'Baclaran',
            'boundary' => 1800,
            'status' => 'active',
        ]);

        // -------------------------
        // VEHICLES (no hardcoded IDs)
        // -------------------------
        $vehicle1 = Vehicle::create([
            'plate_number' => 'ABC-1234',
            'route_id' => $route1->id,
            'operator' => 'Operator A',
            'status' => 'active',
        ]);

        $vehicle2 = Vehicle::create([
            'plate_number' => 'XYZ-5678',
            'route_id' => $route2->id,
            'operator' => 'Operator B',
            'status' => 'active',
        ]);

        // -------------------------
        // DAILY REMITTANCES
        // -------------------------
        DailyRemittance::create([
            'driver_id' => $driver1->id,
            'pao_id' => $pao1->id,
            'route_id' => $route1->id,
            'vehicle_id' => $vehicle1->id,
            'remittance_date' => Carbon::today(),

            'total_collection' => 5000,
            'total_expenses' => 1000,
            'boundary' => 1500,

            'net_remittance' => 2500,

            'is_short_remittance' => false,
            'short_amount' => 0,

            'driver_share' => 1250,
            'pao_share' => 1250,

            'driver_amount_paid' => 1250,
            'driver_status' => 'paid',

            'pao_amount_paid' => 1250,
            'pao_status' => 'paid',

            'resolution_notes' => null,
            'resolved_at' => null,
            'status' => 'completed',
        ]);

        DailyRemittance::create([
            'driver_id' => $driver2->id,
            'pao_id' => $pao2->id,
            'route_id' => $route2->id,
            'vehicle_id' => $vehicle2->id,
            'remittance_date' => Carbon::yesterday(),

            'total_collection' => 4000,
            'total_expenses' => 1200,
            'boundary' => 1800,

            'net_remittance' => 1000,

            'is_short_remittance' => true,
            'short_amount' => 500,

            'driver_share' => 500,
            'pao_share' => 500,

            'driver_amount_paid' => 300,
            'driver_status' => 'partial',

            'pao_amount_paid' => 500,
            'pao_status' => 'paid',

            'resolution_notes' => 'Driver under-remitted',
            'resolved_at' => now(),
            'status' => 'short',
        ]);
    }
}