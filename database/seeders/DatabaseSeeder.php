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
        $this->call([StatutoryDeductions_Seeder::class]);
        $this->call([Pao_Seeder::class]);
        $this->call([AttendanceSeeder::class]);
        $this->call([OvertimeUndertimeSeeder::class]);
        $this->call([AllowanceDeductionSeeder::class]);
        $this->call([LeaveTypeSeeder::class]);
        $this->call(RemittanceSeeder::class);
        // Accounting module seeders (order matters!)
        $this->call(GLAccountSeeder::class);
        $this->call(AccountingPeriodSeeder::class);
        $this->call(ExpenseCategorySeeder::class);
        $this->call(CostCenterSeeder::class);
    }
}