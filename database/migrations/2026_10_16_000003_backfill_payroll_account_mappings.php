<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'other_payroll_deductions_payable' => [
                'account_code' => '2070',
                'account_type' => 'Liability',
                'label' => 'Other Payroll Deductions Payable',
            ],
            'employer_contributions_expense' => [
                'account_code' => '5050',
                'account_type' => 'Expense',
                'label' => 'Employer Government Contributions Expense',
            ],
        ];

        foreach ($defaults as $mappingKey => $default) {
            $account = DB::table('chart_of_accounts')
                ->where('account_code', $default['account_code'])
                ->first();

            if (!$account) {
                continue;
            }

            if ($account->account_type !== $default['account_type'] || !(bool) $account->is_active) {
                throw new RuntimeException(
                    "Cannot backfill {$mappingKey}: active account {$default['account_code']} ({$default['account_type']}) is required."
                );
            }

            $now = now();
            DB::table('account_mappings')->insertOrIgnore([
                'mapping_key' => $mappingKey,
                'label' => $default['label'],
                'account_id' => $account->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Leave payroll mappings intact to avoid disrupting configured accounting.
    }
};