<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatutoryDeductions_Seeder extends Seeder
{
    public function run()
    {
        // SSS Contributions 2025
        $sssContributions = [
            ['min_salary' => 0, 'max_salary' => 5249.99, 'ee' => 250, 'er' => 510, 'note' => null],
            ['min_salary' => 5250, 'max_salary' => 5749.99, 'ee' => 275, 'er' => 560, 'note' => null],
            ['min_salary' => 5750, 'max_salary' => 6249.99, 'ee' => 300, 'er' => 610, 'note' => null],
            ['min_salary' => 6250, 'max_salary' => 6749.99, 'ee' => 325, 'er' => 660, 'note' => null],
            ['min_salary' => 6750, 'max_salary' => 7249.99, 'ee' => 350, 'er' => 710, 'note' => null],
            ['min_salary' => 7250, 'max_salary' => 7749.99, 'ee' => 375, 'er' => 760, 'note' => null],
            ['min_salary' => 7750, 'max_salary' => 8249.99, 'ee' => 400, 'er' => 810, 'note' => null],
            ['min_salary' => 8250, 'max_salary' => 8749.99, 'ee' => 425, 'er' => 860, 'note' => null],
            ['min_salary' => 8750, 'max_salary' => 9249.99, 'ee' => 450, 'er' => 910, 'note' => null],
            ['min_salary' => 9250, 'max_salary' => 9749.99, 'ee' => 475, 'er' => 960, 'note' => null],
            ['min_salary' => 9750, 'max_salary' => 10249.99, 'ee' => 500, 'er' => 1010, 'note' => null],
            ['min_salary' => 10250, 'max_salary' => 10749.99, 'ee' => 525, 'er' => 1060, 'note' => null],
            ['min_salary' => 10750, 'max_salary' => 11249.99, 'ee' => 550, 'er' => 1110, 'note' => null],
            ['min_salary' => 11250, 'max_salary' => 11749.99, 'ee' => 575, 'er' => 1160, 'note' => null],
            ['min_salary' => 11750, 'max_salary' => 12249.99, 'ee' => 600, 'er' => 1210, 'note' => null],
            ['min_salary' => 12250, 'max_salary' => 12749.99, 'ee' => 625, 'er' => 1260, 'note' => null],
            ['min_salary' => 12750, 'max_salary' => 13249.99, 'ee' => 650, 'er' => 1310, 'note' => null],
            ['min_salary' => 13250, 'max_salary' => 13749.99, 'ee' => 675, 'er' => 1360, 'note' => null],
            ['min_salary' => 13750, 'max_salary' => 14249.99, 'ee' => 700, 'er' => 1410, 'note' => null],
            ['min_salary' => 14250, 'max_salary' => 14749.99, 'ee' => 725, 'er' => 1460, 'note' => null],
            ['min_salary' => 14750, 'max_salary' => 15249.99, 'ee' => 750, 'er' => 1530, 'note' => null],
            ['min_salary' => 15250, 'max_salary' => 15749.99, 'ee' => 775, 'er' => 1580, 'note' => null],
            ['min_salary' => 15750, 'max_salary' => 16249.99, 'ee' => 800, 'er' => 1630, 'note' => null],
            ['min_salary' => 16250, 'max_salary' => 16749.99, 'ee' => 825, 'er' => 1680, 'note' => null],
            ['min_salary' => 16750, 'max_salary' => 17249.99, 'ee' => 850, 'er' => 1730, 'note' => null],
            ['min_salary' => 17250, 'max_salary' => 17749.99, 'ee' => 875, 'er' => 1780, 'note' => null],
            ['min_salary' => 17750, 'max_salary' => 18249.99, 'ee' => 900, 'er' => 1830, 'note' => null],
            ['min_salary' => 18250, 'max_salary' => 18749.99, 'ee' => 925, 'er' => 1880, 'note' => null],
            ['min_salary' => 18750, 'max_salary' => 19249.99, 'ee' => 950, 'er' => 1930, 'note' => null],
            ['min_salary' => 19250, 'max_salary' => 19749.99, 'ee' => 975, 'er' => 1980, 'note' => null],

            // Salaries above 20,000 – apply MPF up to 35,000
            ['min_salary' => 19750, 'max_salary' => 20000, 'ee' => 1000, 'er' => 2000, 'note' => 'MPF applies above ₱20,000'],
            ['min_salary' => 20000, 'max_salary' => 999999999, 'ee' => 1000, 'er' => 2000, 'note' => 'Max SSS contribution'],
        ];

        foreach ($sssContributions as $row) {
            DB::table('statutory_deductions')->insert([
                'name' => 'SSS',
                'min_salary' => $row['min_salary'],
                'max_salary' => $row['max_salary'],
                'employee_share' => $row['ee'],
                'employer_share' => $row['er'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Pag-IBIG / HDMF contributions (for reference)
        $pagibigContributions = [['min_salary' => 0, 'max_salary' => 1500, 'ee' => 1, 'er' => 2, 'note' => null], ['min_salary' => 1500.01, 'max_salary' => 999999999, 'ee' => 2, 'er' => 2, 'note' => null]];

        foreach ($pagibigContributions as $row) {
            DB::table('statutory_deductions')->insert([
                'name' => 'Pag-IBIG',
                'min_salary' => $row['min_salary'],
                'max_salary' => $row['max_salary'],
                'employee_share' => $row['ee'],
                'employer_share' => $row['er'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
