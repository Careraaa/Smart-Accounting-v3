<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([ShiftSeeder::class]);
        $this->call([Users_Seeder::class]);
        $this->call([UserExtrasSeeder::class]);
        $this->call([StatutoryDeductions_Seeder::class]);
        $this->call([Pao_Seeder::class]);
        $this->call([AttendanceSeeder::class]);
        $this->call([AllowanceDeductionSeeder::class]);
        $this->call([LeaveTypeSeeder::class]);
        $this->call([LeaveBalanceSeeder::class]);
        $this->call([EmployeeAttachmentsSeeder::class]);
        $this->call([EmployeeRelationsSeeder::class]);
        $this->call([PayrollCutoffScheduleSeeder::class]);
        $this->call([PayrollSeeder::class]);
        $this->call([NotificationSeeder::class]);
        $this->call([RemittanceSeeder::class]);
        $this->call([HolidaySeeder::class]);


    }
}