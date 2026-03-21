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
                'name' => 'Special Leave Privilege',
                'abbreviation' => 'SPL',
                'days_allowed' => 3,
                'carry_over' => false,
                'description' => 'Special privilege leave for government employees',
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
                'name' => 'VAWC Leave',
                'abbreviation' => 'VAWC',
                'days_allowed' => 10,
                'carry_over' => false,
                'description' => 'Violence Against Women and Children leave',
                'status' => 'active',
            ],
            [
                'name' => 'Solo Parent Leave',
                'abbreviation' => 'SPL',
                'days_allowed' => 7,
                'carry_over' => false,
                'description' => 'Leave for solo parents as per Solo Parents Welfare Act',
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
