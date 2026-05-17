<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\User;
use Illuminate\Database\Seeder;

class BonusSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('role', 'superadmin')->first();

        Bonus::updateOrCreate(
            ['code' => Bonus::CODE_THIRTEENTH_MONTH],
            [
                'name' => '13th Month Pay',
                'description' => 'Mandatory 13th month pay based on total basic salary earned within the calendar year divided by 12. Only basic salary is included; overtime, allowances, and other earnings are excluded.',
                'type' => Bonus::TYPE_MANDATORY,
                'computation_method' => Bonus::METHOD_AUTO,
                'formula' => '(total_basic_salary / 12)',
                'fixed_amount' => null,
                'percentage_value' => null,
                'is_mandatory' => true,
                'is_system_generated' => true,
                'status' => 'active',
                'year' => (int) now()->year,
                'payroll_period' => 'Annual',
                'created_by' => $creator?->id,
            ]
        );
    }
}
