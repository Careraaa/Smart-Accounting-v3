<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;

class SeedFutureHolidays extends Command
{
    protected $signature = 'holidays:seed-future';
    protected $description = 'Seed Philippine holidays for 5 years starting from the current year (skips years that already have holidays)';

    public function handle(): int
    {
        $currentYear = (int) date('Y');
        $yearsToSeed = range($currentYear, $currentYear + 4);

        $existingYears = Holiday::selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $yearsNeedingSeeds = array_diff($yearsToSeed, $existingYears);

        if (empty($yearsNeedingSeeds)) {
            $this->info('All years (' . implode(', ', $yearsToSeed) . ') already have holidays. Nothing to do.');
            return self::SUCCESS;
        }

        $holidays = [];

        foreach ($yearsNeedingSeeds as $year) {
            $holidays = array_merge($holidays, [
                ['name' => 'New Year\'s Day', 'date' => "$year-01-01", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Araw ng Kagitingan', 'date' => "$year-04-09", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Labor Day', 'date' => "$year-05-01", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Independence Day', 'date' => "$year-06-12", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'National Heroes Day', 'date' => "$year-08-31", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Bonifacio Day', 'date' => "$year-11-30", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Christmas Day', 'date' => "$year-12-25", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Rizal Day', 'date' => "$year-12-30", 'type' => 'regular', 'created_at' => now(), 'updated_at' => now()],

                ['name' => 'EDSA People Power Revolution', 'date' => "$year-02-25", 'type' => 'special', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Ninoy Aquino Day', 'date' => "$year-08-21", 'type' => 'special', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'All Saints\' Day', 'date' => "$year-11-01", 'type' => 'special', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Christmas Eve', 'date' => "$year-12-24", 'type' => 'special', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'New Year\'s Eve', 'date' => "$year-12-31", 'type' => 'special', 'created_at' => now(), 'updated_at' => now()],
            ]);

            $this->info("Seeded 13 holidays for $year.");
        }

        Holiday::insert($holidays);

        $this->newLine();
        $this->info('Done! Seeded ' . count($holidays) . ' holidays for years: ' . implode(', ', $yearsNeedingSeeds) . '.');

        return self::SUCCESS;
    }
}
