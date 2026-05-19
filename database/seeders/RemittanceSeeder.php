<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemittanceSeeder extends Seeder
{
    public function run(): void
    {
        $driver1 = Driver::updateOrCreate(
            ['license_number' => 'N01-23-123456'],
            [
                'name' => 'Ramon Villanueva',
                'contact_number' => '09171234567',
                'gender' => 'male',
                'email' => 'ramon.villanueva@smartaccounting.ph',
                'address' => '123 Rizal St., Brgy. Commonwealth, Quezon City, Metro Manila',
                'date_of_hire' => '2022-03-15',
                'status' => 'active',
            ]
        );

        $driver2 = Driver::updateOrCreate(
            ['license_number' => 'N02-45-654321'],
            [
                'name' => 'Pedro Santos',
                'contact_number' => '09987654321',
                'gender' => 'male',
                'email' => 'pedro.santos@smartaccounting.ph',
                'address' => '45 Bonifacio Ave., Brgy. 289, Manila, Metro Manila',
                'date_of_hire' => '2023-01-10',
                'status' => 'active',
            ]
        );

        $driver3 = Driver::updateOrCreate(
            ['license_number' => 'N03-18-778899'],
            [
                'name' => 'Gilbert Cruz',
                'contact_number' => '09181239876',
                'gender' => 'male',
                'email' => 'gilbert.cruz@smartaccounting.ph',
                'address' => '12 MacArthur Hwy., Guiguinto, Bulacan',
                'date_of_hire' => '2024-06-01',
                'status' => 'active',
            ]
        );

        $pao1 = PAO::updateOrCreate(
            ['email' => 'maria.lopez@smartaccounting.ph'],
            [
                'name' => 'Maria Lopez',
                'contact_number' => '09170000001',
                'gender' => 'female',
                'address' => '88 C. Raymundo Ave., Brgy. Rosario, Pasig City, Metro Manila',
                'date_of_hire' => '2021-04-01',
                'status' => 'active',
            ]
        );

        $pao2 = PAO::updateOrCreate(
            ['email' => 'ana.reyes@smartaccounting.ph'],
            [
                'name' => 'Ana Reyes',
                'contact_number' => '09171111112',
                'gender' => 'female',
                'address' => '210 Kalayaan Ave., Brgy. Poblacion, Makati City, Metro Manila',
                'date_of_hire' => '2023-08-15',
                'status' => 'active',
            ]
        );

        $route1 = Route::updateOrCreate(
            ['route_name' => 'Cubao to Fairview'],
            ['origin' => 'Cubao', 'destination' => 'Fairview', 'boundary' => 1500, 'status' => 'active']
        );

        $route2 = Route::updateOrCreate(
            ['route_name' => 'Divisoria to Baclaran'],
            ['origin' => 'Divisoria', 'destination' => 'Baclaran', 'boundary' => 1800, 'status' => 'active']
        );

        $vehicle1 = Vehicle::updateOrCreate(
            ['plate_number' => 'ABC-1234'],
            ['route_id' => $route1->id, 'operator' => 'Operator A', 'status' => 'active']
        );

        $vehicle2 = Vehicle::updateOrCreate(
            ['plate_number' => 'XYZ-5678'],
            ['route_id' => $route2->id, 'operator' => 'Operator B', 'status' => 'active']
        );

        $vehicle3 = Vehicle::updateOrCreate(
            ['plate_number' => 'NCR-9021'],
            ['route_id' => $route1->id, 'operator' => 'Operator A', 'status' => 'active']
        );

        DB::table('daily_remittances')->truncate();

        $assignments = [
            [$driver1, $pao1, $route1, $vehicle1],
            [$driver2, $pao2, $route2, $vehicle2],
            [$driver3, $pao1, $route1, $vehicle3],
        ];

        $workDays = SeedConfig::workDaysInRange();
        $rows = [];
        $dayIndex = 0;

        foreach ($workDays as $date) {
            if ($dayIndex % 2 !== 0) {
                $dayIndex++;
                continue;
            }

            foreach ($assignments as $ai => [$driver, $pao, $route, $vehicle]) {
                if (($dayIndex + $ai) % 5 === 0) {
                    continue;
                }

                $boundary = (float) ($route->boundary ?? 1500);
                $collection = round($boundary * (1.15 + (SeedConfig::hashFloat($dayIndex, 'col-' . $ai) * 0.35)), 2);
                $expenses = round(200 + (SeedConfig::hashFloat($dayIndex, 'exp-' . $ai) * 450), 2);
                $net = round($collection - $expenses - $boundary, 2);
                $isShort = $net < 0;
                $shortAmount = $isShort ? abs($net) : 0;

                $statusRoll = SeedConfig::hashFloat($dayIndex, 'st-' . $ai);
                $status = $statusRoll > 0.85 ? 'pending' : ($statusRoll > 0.1 ? 'approved' : 'rejected');

                $rows[] = [
                    'driver_id' => $driver->id,
                    'pao_id' => $pao->id,
                    'route_id' => $route->id,
                    'vehicle_id' => $vehicle->id,
                    'remittance_date' => $date,
                    'total_collection' => $collection,
                    'total_expenses' => $expenses,
                    'boundary' => $boundary,
                    'net_remittance' => $net,
                    'is_short_remittance' => $isShort ? 1 : 0,
                    'short_amount' => $shortAmount,
                    'driver_share' => $isShort ? round($shortAmount * 0.6, 2) : null,
                    'pao_share' => $isShort ? round($shortAmount * 0.4, 2) : null,
                    'driver_amount_paid' => 0,
                    'driver_status' => $isShort ? 'pending' : 'paid',
                    'pao_amount_paid' => 0,
                    'pao_status' => $isShort ? 'pending' : 'paid',
                    'resolution_notes' => null,
                    'resolved_at' => null,
                    'status' => $status,
                    'created_at' => $date . ' 20:00:00',
                    'updated_at' => $date . ' 20:30:00',
                ];
            }
            $dayIndex++;
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('daily_remittances')->insert($chunk);
        }

        $this->command?->info('RemittanceSeeder: ' . count($rows) . ' daily remittances (' . SeedConfig::RANGE_START . ' to ' . SeedConfig::RANGE_END . ').');
    }
}
