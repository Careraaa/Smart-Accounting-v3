<?php

namespace Database\Seeders;

use App\Models\WithholdingTax;
use Illuminate\Database\Seeder;

class WithholdingTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // BIR Prescribed Withholding Tax (PWC) Table - Effective January 1, 2023
        // Reference: https://www.bir.gov.ph/WithHoldingTax

        $taxes = [
            // DAILY RATES
            [
                'name' => 'Daily - Bracket 1',
                'min_salary' => 0,
                'max_salary' => 685,
                'employee_share' => 0,
                'percentage_employee' => 0,
                'description' => 'Daily',
            ],
            [
                'name' => 'Daily - Bracket 2',
                'min_salary' => 685.01,
                'max_salary' => 1095,
                'employee_share' => 0,
                'percentage_employee' => 15,
                'description' => 'Daily (₱0 + 15% over ₱685)',
            ],
            [
                'name' => 'Daily - Bracket 3',
                'min_salary' => 1096,
                'max_salary' => 2191,
                'employee_share' => 61.65,
                'percentage_employee' => 20,
                'description' => 'Daily (₱61.65 + 20% over ₱1,096)',
            ],
            [
                'name' => 'Daily - Bracket 4',
                'min_salary' => 2192,
                'max_salary' => 5478,
                'employee_share' => 280.85,
                'percentage_employee' => 25,
                'description' => 'Daily (₱280.85 + 25% over ₱2,192)',
            ],
            [
                'name' => 'Daily - Bracket 5',
                'min_salary' => 5479,
                'max_salary' => 21917,
                'employee_share' => 1102.60,
                'percentage_employee' => 30,
                'description' => 'Daily (₱1,102.60 + 30% over ₱5,479)',
            ],
            [
                'name' => 'Daily - Bracket 6',
                'min_salary' => 21918,
                'max_salary' => 999999,
                'employee_share' => 6036.30,
                'percentage_employee' => 35,
                'description' => 'Daily (₱6,036.30 + 35% over ₱21,918)',
            ],

            // WEEKLY RATES
            [
                'name' => 'Weekly - Bracket 1',
                'min_salary' => 0,
                'max_salary' => 4808,
                'employee_share' => 0,
                'percentage_employee' => 0,
                'description' => 'Weekly',
            ],
            [
                'name' => 'Weekly - Bracket 2',
                'min_salary' => 4808.01,
                'max_salary' => 7691,
                'employee_share' => 0,
                'percentage_employee' => 15,
                'description' => 'Weekly (₱0 + 15% over ₱4,808)',
            ],
            [
                'name' => 'Weekly - Bracket 3',
                'min_salary' => 7692,
                'max_salary' => 15384,
                'employee_share' => 432.60,
                'percentage_employee' => 20,
                'description' => 'Weekly (₱432.60 + 20% over ₱7,692)',
            ],
            [
                'name' => 'Weekly - Bracket 4',
                'min_salary' => 15385,
                'max_salary' => 39461,
                'employee_share' => 1971.20,
                'percentage_employee' => 25,
                'description' => 'Weekly (₱1,971.20 + 25% over ₱15,385)',
            ],
            [
                'name' => 'Weekly - Bracket 5',
                'min_salary' => 39462,
                'max_salary' => 153845,
                'employee_share' => 7760.45,
                'percentage_employee' => 30,
                'description' => 'Weekly (₱7,760.45 + 30% over ₱39,462)',
            ],
            [
                'name' => 'Weekly - Bracket 6',
                'min_salary' => 153846,
                'max_salary' => 999999,
                'employee_share' => 42355.65,
                'percentage_employee' => 35,
                'description' => 'Weekly (₱42,355.65 + 35% over ₱153,846)',
            ],

            // SEMI-MONTHLY RATES
            [
                'name' => 'Semi-monthly - Bracket 1',
                'min_salary' => 0,
                'max_salary' => 10417,
                'employee_share' => 0,
                'percentage_employee' => 0,
                'description' => 'Semi-monthly',
            ],
            [
                'name' => 'Semi-monthly - Bracket 2',
                'min_salary' => 10417.01,
                'max_salary' => 16666,
                'employee_share' => 0,
                'percentage_employee' => 15,
                'description' => 'Semi-monthly (₱0 + 15% over ₱10,417)',
            ],
            [
                'name' => 'Semi-monthly - Bracket 3',
                'min_salary' => 16667,
                'max_salary' => 33332,
                'employee_share' => 937.50,
                'percentage_employee' => 20,
                'description' => 'Semi-monthly (₱937.50 + 20% over ₱16,667)',
            ],
            [
                'name' => 'Semi-monthly - Bracket 4',
                'min_salary' => 33333,
                'max_salary' => 83332,
                'employee_share' => 4270.70,
                'percentage_employee' => 25,
                'description' => 'Semi-monthly (₱4,270.70 + 25% over ₱33,333)',
            ],
            [
                'name' => 'Semi-monthly - Bracket 5',
                'min_salary' => 83333,
                'max_salary' => 333332,
                'employee_share' => 16770.70,
                'percentage_employee' => 30,
                'description' => 'Semi-monthly (₱16,770.70 + 30% over ₱83,333)',
            ],
            [
                'name' => 'Semi-monthly - Bracket 6',
                'min_salary' => 333333,
                'max_salary' => 999999,
                'employee_share' => 91770.70,
                'percentage_employee' => 35,
                'description' => 'Semi-monthly (₱91,770.70 + 35% over ₱333,333)',
            ],

            // MONTHLY RATES
            [
                'name' => 'Monthly - Bracket 1',
                'min_salary' => 0,
                'max_salary' => 20833,
                'employee_share' => 0,
                'percentage_employee' => 0,
                'description' => 'Monthly',
            ],
            [
                'name' => 'Monthly - Bracket 2',
                'min_salary' => 20833.01,
                'max_salary' => 33332,
                'employee_share' => 0,
                'percentage_employee' => 15,
                'description' => 'Monthly (₱0 + 15% over ₱20,833)',
            ],
            [
                'name' => 'Monthly - Bracket 3',
                'min_salary' => 33333,
                'max_salary' => 66666,
                'employee_share' => 1875.00,
                'percentage_employee' => 20,
                'description' => 'Monthly (₱1,875.00 + 20% over ₱33,333)',
            ],
            [
                'name' => 'Monthly - Bracket 4',
                'min_salary' => 66667,
                'max_salary' => 166666,
                'employee_share' => 8541.80,
                'percentage_employee' => 25,
                'description' => 'Monthly (₱8,541.80 + 25% over ₱66,667)',
            ],
            [
                'name' => 'Monthly - Bracket 5',
                'min_salary' => 166667,
                'max_salary' => 666666,
                'employee_share' => 33541.80,
                'percentage_employee' => 30,
                'description' => 'Monthly (₱33,541.80 + 30% over ₱166,667)',
            ],
            [
                'name' => 'Monthly - Bracket 6',
                'min_salary' => 666667,
                'max_salary' => 999999,
                'employee_share' => 183541.80,
                'percentage_employee' => 35,
                'description' => 'Monthly (₱183,541.80 + 35% over ₱666,667)',
            ],
        ];

        foreach ($taxes as $tax) {
            WithholdingTax::updateOrCreate(
                ['name' => $tax['name']],
                $tax
            );
        }
    }
}
