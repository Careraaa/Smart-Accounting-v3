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
        // Create payroll cutoff schedules for Batch 1 (5th) and Batch 2 (28th) of each month
        PayrollCutoffSchedule::create([
            'cutoff_day' => 5,
            'label' => 'Batch 1',
            'is_active' => true,
        ]);

        PayrollCutoffSchedule::create([
            'cutoff_day' => 28,
            'label' => 'Batch 2',
            'is_active' => true,
        ]);
    }
}
