<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'boundary_cash' => [
                'account_code' => '1010',
                'account_type' => 'Asset',
                'label' => 'Boundary Payment Cash / Bank',
            ],
            'boundary_revenue' => [
                'account_code' => '4010',
                'account_type' => 'Revenue',
                'label' => 'Boundary Revenue',
            ],
        ];

        foreach ($defaults as $mappingKey => $default) {
            $account = DB::table('chart_of_accounts')
                ->where('account_code', $default['account_code'])
                ->first();

            if (! $account) {
                continue;
            }

            if ($account->account_type !== $default['account_type'] || ! (bool) $account->is_active) {
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
        // Keep configured mappings intact when rolling back.
    }
};
