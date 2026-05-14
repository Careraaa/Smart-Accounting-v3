<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2026;

        Holiday::insert([
            // Regular Holidays - Full pay even if not worked (if worked previous day)
            ['name' => 'New Year\'s Day', 'date' => "$year-01-01", 'type' => 'regular'],
            ['name' => 'Araw ng Kagitingan', 'date' => "$year-04-09", 'type' => 'regular'],
            ['name' => 'Labor Day', 'date' => "$year-05-01", 'type' => 'regular'],
            ['name' => 'Independence Day', 'date' => "$year-06-12", 'type' => 'regular'],
            ['name' => 'National Heroes Day', 'date' => "$year-08-31", 'type' => 'regular'],
            ['name' => 'Bonifacio Day', 'date' => "$year-11-30", 'type' => 'regular'],
            ['name' => 'Christmas Day', 'date' => "$year-12-25", 'type' => 'regular'],
            ['name' => 'Rizal Day', 'date' => "$year-12-30", 'type' => 'regular'],

            // Special Non-Working Holidays - "No work, no pay"
            ['name' => 'EDSA People Power Revolution', 'date' => "$year-02-25", 'type' => 'special'],
            ['name' => 'Ninoy Aquino Day', 'date' => "$year-08-21", 'type' => 'special'],
            ['name' => 'All Saints\' Day', 'date' => "$year-11-01", 'type' => 'special'],
            ['name' => 'Christmas Eve', 'date' => "$year-12-24", 'type' => 'special'],
            ['name' => 'New Year\'s Eve', 'date' => "$year-12-31", 'type' => 'special'],
        ]);
    }
}
