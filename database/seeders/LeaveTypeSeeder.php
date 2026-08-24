<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Vacation Leave',
                'abbreviation' => 'VL',
                'days_allowed' => 15,
                'carry_over' => true,
                'description' => 'Annual vacation leave for rest and recreation',
                'status' => 'active',
            ],
            [
                'name' => 'Sick Leave',
                'abbreviation' => 'SL',
                'days_allowed' => 15,
                'carry_over' => true,
                'description' => 'Leave for illness or medical appointments',
                'status' => 'active',
            ],
            [
                'name' => 'Maternity Leave',
                'abbreviation' => 'ML',
                'days_allowed' => 105,
                'carry_over' => false,
                'description' => 'Maternity leave for female employees (105 days)',
                'status' => 'active',
            ],
            [
                'name' => 'Paternity Leave',
                'abbreviation' => 'PL',
                'days_allowed' => 7,
                'carry_over' => false,
                'description' => 'Paternity leave for male employees',
                'status' => 'active',
            ],
            [
                'name' => 'Emergency Leave',
                'abbreviation' => 'EL',
                'days_allowed' => 5,
                'carry_over' => false,
                'description' => 'Leave for emergency situations or family emergencies',
                'status' => 'active',
            ],
            [
                'name' => 'Bereavement Leave',
                'abbreviation' => 'BL',
                'days_allowed' => 5,
                'carry_over' => false,
                'description' => 'Leave for the death of an immediate family member',
                'status' => 'active',
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                ['name' => $leaveType['name']],
                $leaveType
            );
        }
    }
}
