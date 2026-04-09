<?php

namespace Database\Seeders;

use App\Models\AccountingPeriod;
use Illuminate\Database\Seeder;

class AccountingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Create periods for the current fiscal year and next 11 months
        for ($month = 1; $month <= 12; $month++) {
            $year = $month < $currentMonth ? $currentYear + 1 : $currentYear;
            
            $startDate = now()
                ->setYear($year)
                ->setMonth($month)
                ->startOfMonth();
            
            $endDate = $startDate->copy()->endOfMonth();
            
            $periodName = $startDate->format('F Y');

            AccountingPeriod::firstOrCreate(
                [
                    'period_number' => $month,
                    'fiscal_year' => $year,
                ],
                [
                    'period_name' => $periodName,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'is_current' => $month == $currentMonth && $year == $currentYear,
                ]
            );
        }
    }
}
