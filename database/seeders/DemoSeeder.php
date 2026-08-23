<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ShiftSeeder::class,
            Users_Seeder::class,
            UserExtrasSeeder::class,
            StatutoryDeductions_Seeder::class,
            WithholdingTaxSeeder::class,
            Pao_Seeder::class,
            AttendanceSeeder::class,
            AllowanceDeductionSeeder::class,
            LeaveTypeSeeder::class,
            LeaveBalanceSeeder::class,
            EmployeeAttachmentsSeeder::class,
            EmployeeRelationsSeeder::class,
            PayrollCutoffScheduleSeeder::class,
            PayrollSeeder::class,
            NotificationSeeder::class,
            RemittanceSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}