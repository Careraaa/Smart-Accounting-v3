<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use App\Models\Route;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cost centers based on routes
        $routes = Route::all();
        
        foreach ($routes as $route) {
            CostCenter::firstOrCreate(
                [
                    'cost_center_code' => 'RT-' . strtoupper(substr($route->route_name ?? 'Route', 0, 3)),
                    'cost_center_type' => 'Route',
                ],
                [
                    'cost_center_name' => $route->route_name ?? 'Route ' . $route->id,
                    'description' => 'Cost center for ' . ($route->route_name ?? 'Route ' . $route->id),
                    'route_id' => $route->id,
                    'is_active' => true,
                ]
            );
        }

        // Create department cost centers
        $departments = [
            ['code' => 'ADMIN', 'name' => 'Administrative', 'description' => 'Administrative department'],
            ['code' => 'OPS', 'name' => 'Operations', 'description' => 'Operations department'],
            ['code' => 'MAINT', 'name' => 'Maintenance', 'description' => 'Vehicle maintenance department'],
            ['code' => 'HR', 'name' => 'Human Resources', 'description' => 'Human resources department'],
            ['code' => 'ACCT', 'name' => 'Accounting', 'description' => 'Accounting department'],
        ];

        foreach ($departments as $dept) {
            CostCenter::firstOrCreate(
                ['cost_center_code' => $dept['code']],
                [
                    'cost_center_name' => $dept['name'],
                    'description' => $dept['description'],
                    'cost_center_type' => 'Department',
                    'is_active' => true,
                ]
            );
        }
    }
}
