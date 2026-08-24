<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route as TransportRoute;
use App\Models\Vehicle;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemittanceSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Drivers
        |--------------------------------------------------------------------------
        */

        $driver1 = Driver::updateOrCreate(
            ['license_number' => 'N01-23-123456'],
            [
                'name' => 'Ramon Villanueva',
                'contact_number' => '09171234567',
                'gender' => 'male',
                'email' => 'ramon.villanueva@gmail.com',
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
                'email' => 'pedro.santos@gmail.com',
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
                'email' => 'gilbert.cruz@gmail.com',
                'address' => '12 MacArthur Hwy., Guiguinto, Bulacan',
                'date_of_hire' => '2024-06-01',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PAOs
        |--------------------------------------------------------------------------
        */

        $pao1 = PAO::updateOrCreate(
            ['email' => 'maria.lopez@gmail.com'],
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
            ['email' => 'ana.reyes@gmail.com'],
            [
                'name' => 'Ana Reyes',
                'contact_number' => '09171111112',
                'gender' => 'female',
                'address' => '210 Kalayaan Ave., Brgy. Poblacion, Makati City, Metro Manila',
                'date_of_hire' => '2023-08-15',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Routes
        |--------------------------------------------------------------------------
        */

        $route1 = TransportRoute::updateOrCreate(
            ['route_name' => 'Fortune to Cubao'],
            [
                'origin' => 'Fortune',
                'destination' => 'Cubao',
                'boundary' => 3500,
                'status' => 'active',
            ]
        );

        $route2 = TransportRoute::updateOrCreate(
            ['route_name' => 'SSS Village to Cubao'],
            [
                'origin' => 'SSS Village',
                'destination' => 'Cubao',
                'boundary' => 3000,
                'status' => 'active',
            ]
        );

        $route3 = TransportRoute::updateOrCreate(
            ['route_name' => 'Montalban to Cubao'],
            [
                'origin' => 'Montalban',
                'destination' => 'Cubao',
                'boundary' => 3500,
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Vehicles
        |--------------------------------------------------------------------------
        */

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

        $vehicle3 = Vehicle::updateOrCreate(
            ['plate_number' => 'NCR-9021'],
            [
                'route_id' => $route1->id,
                'operator' => 'Operator A',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Clear Existing Data
        |--------------------------------------------------------------------------
        */

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('daily_remittances')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        $assignments = [
            [$driver1, $pao1, $route1, $vehicle1],
            [$driver2, $pao2, $route2, $vehicle2],
            [$driver3, $pao1, $route1, $vehicle3],
        ];

        $workDays = SeedConfig::workDaysInRange();

        $rows = [];
        $dayIndex = 0;

        /*
        |--------------------------------------------------------------------------
        | Generate Daily Remittances
        |--------------------------------------------------------------------------
        */

        foreach ($workDays as $date) {

            foreach ($assignments as $ai => [$driver, $pao, $route, $vehicle]) {

                $boundary = (float) ($route->boundary ?? 1500);

                /*
                |--------------------------------------------------------------------------
                | Route-Based Collection
                |--------------------------------------------------------------------------
                */

                if ($route->route_name === 'Divisoria to Baclaran') {
                    $baseCollection = 11000;
                } else {
                    $baseCollection = 8500;
                }

                /*
                |--------------------------------------------------------------------------
                | Force Some Short Remittances
                |--------------------------------------------------------------------------
                |
                | Every few days, collections become lower
                | and expenses become higher to simulate
                | bad operations / low passenger count.
                |--------------------------------------------------------------------------
                */

                $shouldBeShort = (($dayIndex + $ai) % 6 === 0);

                /*
                |--------------------------------------------------------------------------
                | Total Collection
                |--------------------------------------------------------------------------
                */

                if ($shouldBeShort) {

                    // Low collection days
                    $collection = round(
                        5000 +
                        (SeedConfig::hashFloat($dayIndex, 'short-col-' . $ai) * 1800),
                        2
                    );

                } else {

                    // Normal days
                    $collection = round(
                        $baseCollection +
                        (SeedConfig::hashFloat($dayIndex, 'col-' . $ai) * 2500),
                        2
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Total Expenses
                |--------------------------------------------------------------------------
                */

                if ($shouldBeShort) {

                    // High expenses during bad days
                    $expenses = round(
                        4200 +
                        (SeedConfig::hashFloat($dayIndex, 'short-exp-' . $ai) * 1800),
                        2
                    );

                } else {

                    // Normal expenses
                    $expenses = round(
                        3000 +
                        (SeedConfig::hashFloat($dayIndex, 'exp-' . $ai) * 2200),
                        2
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Actual Earnings
                |--------------------------------------------------------------------------
                */

                $actualEarnings = round(
                    $collection - $expenses,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | Net Remittance Logic
                |--------------------------------------------------------------------------
                |
                | If earnings exceed boundary:
                |     net remittance = boundary
                |
                | If earnings are below boundary:
                |     net remittance = actual earnings
                |--------------------------------------------------------------------------
                */

                if ($actualEarnings >= $boundary) {
                    $netRemittance = $boundary;
                    $isShort = false;
                    $shortAmount = 0;
                } else {
                    $netRemittance = $actualEarnings;
                    $isShort = true;
                    $shortAmount = round($boundary - $actualEarnings, 2);
                }

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $statusRoll = SeedConfig::hashFloat($dayIndex, 'st-' . $ai);

                $status = $statusRoll > 0.85
                    ? 'pending'
                    : ($statusRoll > 0.10
                        ? 'approved'
                        : 'rejected');

                /*
                |--------------------------------------------------------------------------
                | Insert Row
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'driver_id' => $driver->id,
                    'pao_id' => $pao->id,
                    'route_id' => $route->id,
                    'vehicle_id' => $vehicle->id,

                    'remittance_date' => $date,

                    'total_collection' => $collection,
                    'total_expenses' => $expenses,
                    'boundary' => $boundary,

                    'net_remittance' => $netRemittance,

                    'is_short_remittance' => $isShort ? 1 : 0,
                    'short_amount' => $shortAmount,

                    'driver_share' => $isShort
                        ? round($shortAmount * 0.5, 2)
                        : null,

                    'pao_share' => $isShort
                        ? round($shortAmount * 0.5, 2)
                        : null,

                    'driver_amount_paid' => 0,

                    'driver_status' => $isShort
                        ? 'pending'
                        : 'paid',

                    'pao_amount_paid' => 0,

                    'pao_status' => $isShort
                        ? 'pending'
                        : 'paid',

                    'resolution_notes' => null,
                    'resolved_at' => null,

                    'status' => $status,

                    'created_at' => $date . ' 20:00:00',
                    'updated_at' => $date . ' 20:30:00',
                ];
            }

            $dayIndex++;
        }

        /*
        |--------------------------------------------------------------------------
        | Bulk Insert
        |--------------------------------------------------------------------------
        */

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('daily_remittances')->insert($chunk);
        }

        $this->command?->info(
            'RemittanceSeeder: ' .
            count($rows) .
            ' daily remittances seeded successfully.'
        );
    }
}