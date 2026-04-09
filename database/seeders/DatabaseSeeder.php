<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([Users_Seeder::class]);
        $this->call([UserExtrasSeeder::class]);
        $this->call([StatutoryDeductions_Seeder::class]);
        $this->call([Pao_Seeder::class]);
        $this->call([AttendanceSeeder::class]);
        $this->call([OvertimeUndertimeSeeder::class]);
        $this->call([AllowanceDeductionSeeder::class]);
        $this->call([LeaveTypeSeeder::class]);
        $this->call([EmployeeLeavesSeeder::class]);
        $this->call([EmployeeCashAdvancesSeeder::class]);
        $this->call([EmployeeSalaryLoansSeeder::class]);
        $this->call([EmployeeAttachmentsSeeder::class]);
        $this->call([EmployeeRelationsSeeder::class]);
        $this->call([PayrollSeeder::class]);
        $this->call([NotificationSeeder::class]);
        $this->call(RemittanceSeeder::class);
    }
}