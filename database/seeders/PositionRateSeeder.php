<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\PositionRate;
use Illuminate\Database\Seeder;

class PositionRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'HR' => Department::firstOrCreate(['name' => 'HR'], ['is_active' => true]),
            'Finance' => Department::firstOrCreate(['name' => 'Finance'], ['is_active' => true]),
            'Operations' => Department::firstOrCreate(['name' => 'Operations'], ['is_active' => true]),
            'Sales' => Department::firstOrCreate(['name' => 'Sales'], ['is_active' => true]),
        ];

        $positions = [
            ['name' => 'Administrative Assistant', 'daily_rate' => 500.00, 'department' => 'HR'],
            ['name' => 'Accountant', 'daily_rate' => 650.00, 'department' => 'Finance'],
            ['name' => 'HR Staff', 'daily_rate' => 600.00, 'department' => 'HR'],
            ['name' => 'Supervisor', 'daily_rate' => 800.00, 'department' => 'Operations'],
            ['name' => 'Manager', 'daily_rate' => 1000.00, 'department' => 'Sales'],
        ];

        foreach ($positions as $position) {
            PositionRate::firstOrCreate(
                ['name' => $position['name']],
                [
                    'daily_rate' => $position['daily_rate'],
                    'department_id' => $departments[$position['department']]->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
