<?php

namespace Database\Seeders;

use App\Models\PayrollCutoffSchedule;
use Illuminate\Database\Seeder;

class PayrollCutoffScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create payroll cutoff schedules for bi-monthly periods: 1-15 and 16-30/31
        PayrollCutoffSchedule::firstOrCreate(
            ['cutoff_day' => 15],
            [
                'label' => '1st Half',
                'is_active' => true,
            ]
        );

        PayrollCutoffSchedule::firstOrCreate(
            ['cutoff_day' => 31],
            [
                'label' => '2nd Half',
                'is_active' => true,
            ]
        );
    }
}
