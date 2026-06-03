<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatutoryDeductions_Seeder extends Seeder
{
    public function run()
    {
        // SSS Contributions 2025 (Effective January 2025)
        $sssContributions = [
            ['min_salary' => 0,        'max_salary' => 5249.99,   'ee' => 250,  'er' => 510],
            ['min_salary' => 5250,     'max_salary' => 5749.99,   'ee' => 275,  'er' => 560],
            ['min_salary' => 5750,     'max_salary' => 6249.99,   'ee' => 300,  'er' => 610],
            ['min_salary' => 6250,     'max_salary' => 6749.99,   'ee' => 325,  'er' => 660],
            ['min_salary' => 6750,     'max_salary' => 7249.99,   'ee' => 350,  'er' => 710],
            ['min_salary' => 7250,     'max_salary' => 7749.99,   'ee' => 375,  'er' => 760],
            ['min_salary' => 7750,     'max_salary' => 8249.99,   'ee' => 400,  'er' => 810],
            ['min_salary' => 8250,     'max_salary' => 8749.99,   'ee' => 425,  'er' => 860],
            ['min_salary' => 8750,     'max_salary' => 9249.99,   'ee' => 450,  'er' => 910],
            ['min_salary' => 9250,     'max_salary' => 9749.99,   'ee' => 475,  'er' => 960],
            ['min_salary' => 9750,     'max_salary' => 10249.99,  'ee' => 500,  'er' => 1010],
            ['min_salary' => 10250,    'max_salary' => 10749.99,  'ee' => 525,  'er' => 1060],
            ['min_salary' => 10750,    'max_salary' => 11249.99,  'ee' => 550,  'er' => 1110],
            ['min_salary' => 11250,    'max_salary' => 11749.99,  'ee' => 575,  'er' => 1160],
            ['min_salary' => 11750,    'max_salary' => 12249.99,  'ee' => 600,  'er' => 1210],
            ['min_salary' => 12250,    'max_salary' => 12749.99,  'ee' => 625,  'er' => 1260],
            ['min_salary' => 12750,    'max_salary' => 13249.99,  'ee' => 650,  'er' => 1310],
            ['min_salary' => 13250,    'max_salary' => 13749.99,  'ee' => 675,  'er' => 1360],
            ['min_salary' => 13750,    'max_salary' => 14249.99,  'ee' => 700,  'er' => 1410],
            ['min_salary' => 14250,    'max_salary' => 14749.99,  'ee' => 725,  'er' => 1460],
            ['min_salary' => 14750,    'max_salary' => 15249.99,  'ee' => 750,  'er' => 1530],
            ['min_salary' => 15250,    'max_salary' => 15749.99,  'ee' => 775,  'er' => 1580],
            ['min_salary' => 15750,    'max_salary' => 16249.99,  'ee' => 800,  'er' => 1630],
            ['min_salary' => 16250,    'max_salary' => 16749.99,  'ee' => 825,  'er' => 1680],
            ['min_salary' => 16750,    'max_salary' => 17249.99,  'ee' => 850,  'er' => 1730],
            ['min_salary' => 17250,    'max_salary' => 17749.99,  'ee' => 875,  'er' => 1780],
            ['min_salary' => 17750,    'max_salary' => 18249.99,  'ee' => 900,  'er' => 1830],
            ['min_salary' => 18250,    'max_salary' => 18749.99,  'ee' => 925,  'er' => 1880],
            ['min_salary' => 18750,    'max_salary' => 19249.99,  'ee' => 950,  'er' => 1930],
            ['min_salary' => 19250,    'max_salary' => 19749.99,  'ee' => 975,  'er' => 1980],
            ['min_salary' => 19750,    'max_salary' => 20249.99,  'ee' => 1000, 'er' => 2030],

            // MPF kicks in above ₱20,250 — EE and ER totals include both Regular SS + MPF
            ['min_salary' => 20250,    'max_salary' => 20749.99,  'ee' => 1025, 'er' => 2080],
            ['min_salary' => 20750,    'max_salary' => 21249.99,  'ee' => 1050, 'er' => 2130],
            ['min_salary' => 21250,    'max_salary' => 21749.99,  'ee' => 1075, 'er' => 2180],
            ['min_salary' => 21750,    'max_salary' => 22249.99,  'ee' => 1100, 'er' => 2230],
            ['min_salary' => 22250,    'max_salary' => 22749.99,  'ee' => 1125, 'er' => 2280],
            ['min_salary' => 22750,    'max_salary' => 23249.99,  'ee' => 1150, 'er' => 2330],
            ['min_salary' => 23250,    'max_salary' => 23749.99,  'ee' => 1175, 'er' => 2380],
            ['min_salary' => 23750,    'max_salary' => 24249.99,  'ee' => 1200, 'er' => 2430],
            ['min_salary' => 24250,    'max_salary' => 24749.99,  'ee' => 1225, 'er' => 2480],
            ['min_salary' => 24750,    'max_salary' => 25249.99,  'ee' => 1250, 'er' => 2530],
            ['min_salary' => 25250,    'max_salary' => 25749.99,  'ee' => 1275, 'er' => 2580],
            ['min_salary' => 25750,    'max_salary' => 26249.99,  'ee' => 1300, 'er' => 2630],
            ['min_salary' => 26250,    'max_salary' => 26749.99,  'ee' => 1325, 'er' => 2680],
            ['min_salary' => 26750,    'max_salary' => 27249.99,  'ee' => 1350, 'er' => 2730],
            ['min_salary' => 27250,    'max_salary' => 27749.99,  'ee' => 1375, 'er' => 2780],
            ['min_salary' => 27750,    'max_salary' => 28249.99,  'ee' => 1400, 'er' => 2830],
            ['min_salary' => 28250,    'max_salary' => 28749.99,  'ee' => 1425, 'er' => 2880],
            ['min_salary' => 28750,    'max_salary' => 29249.99,  'ee' => 1450, 'er' => 2930],
            ['min_salary' => 29250,    'max_salary' => 29749.99,  'ee' => 1475, 'er' => 2980],
            ['min_salary' => 29750,    'max_salary' => 30249.99,  'ee' => 1500, 'er' => 3030],
            ['min_salary' => 30250,    'max_salary' => 30749.99,  'ee' => 1525, 'er' => 3080],
            ['min_salary' => 30750,    'max_salary' => 31249.99,  'ee' => 1550, 'er' => 3130],
            ['min_salary' => 31250,    'max_salary' => 31749.99,  'ee' => 1575, 'er' => 3180],
            ['min_salary' => 31750,    'max_salary' => 32249.99,  'ee' => 1600, 'er' => 3230],
            ['min_salary' => 32250,    'max_salary' => 32749.99,  'ee' => 1625, 'er' => 3280],
            ['min_salary' => 32750,    'max_salary' => 33249.99,  'ee' => 1650, 'er' => 3330],
            ['min_salary' => 33250,    'max_salary' => 33749.99,  'ee' => 1675, 'er' => 3380],
            ['min_salary' => 33750,    'max_salary' => 34249.99,  'ee' => 1700, 'er' => 3430],
            ['min_salary' => 34250,    'max_salary' => 34749.99,  'ee' => 1725, 'er' => 3480],
            ['min_salary' => 34750,    'max_salary' => 999999999, 'ee' => 1750, 'er' => 3530], // Max bracket
        ];

        foreach ($sssContributions as $row) {
            DB::table('statutory_deductions')->insert([
                'name'           => 'SSS',
                'min_salary'     => $row['min_salary'],
                'max_salary'     => $row['max_salary'],
                'employee_share' => $row['ee'],
                'employer_share' => $row['er'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // Pag-IBIG / HDMF contributions (percentage-based)
        $pagibigContributions = [['min_salary' => 0, 'max_salary' => 1500, 'ee_percent' => 1, 'er_percent' => 2, 'note' => null], ['min_salary' => 1500.01, 'max_salary' => 999999999, 'ee_percent' => 2, 'er_percent' => 2, 'note' => null]];

        foreach ($pagibigContributions as $row) {
            DB::table('statutory_deductions')->insert([
                'name' => 'Pag-IBIG',
                'min_salary' => $row['min_salary'],
                'max_salary' => $row['max_salary'],
                'percentage_employee' => $row['ee_percent'],
                'percentage_employer' => $row['er_percent'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // PhilHealth / Medicare contributions (2025)
        // Premium is divided equally between employee (50%) and employer (50%)
        $philhealthContributions = [
            ['min_salary' => 0, 'max_salary' => 10000, 'ee_premium' => 250, 'er_premium' => 250, 'note' => 'Fixed ₱500 premium split 50-50'],
            ['min_salary' => 10000.01, 'max_salary' => 99999.99, 'ee_percent' => 2.5, 'er_percent' => 2.5, 'note' => '5% of salary split 50-50'],
            ['min_salary' => 100000, 'max_salary' => 999999999, 'ee_premium' => 2500, 'er_premium' => 2500, 'note' => 'Fixed ₱5,000 premium split 50-50'],
        ];

        foreach ($philhealthContributions as $row) {
            DB::table('statutory_deductions')->insert([
                'name' => 'PhilHealth',
                'min_salary' => $row['min_salary'],
                'max_salary' => $row['max_salary'],
                'employee_share' => $row['ee_premium'] ?? null,
                'employer_share' => $row['er_premium'] ?? null,
                'percentage_employee' => $row['ee_percent'] ?? null,
                'percentage_employer' => $row['er_percent'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
