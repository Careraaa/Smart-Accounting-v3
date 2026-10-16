<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chart_of_accounts', 'is_contra')) {
            Schema::table('chart_of_accounts', function (Blueprint $table) {
                $table->boolean('is_contra')->default(false);
            });
        }

        if (!Schema::hasColumn('payroll_deductions', 'accounting_treatment')) {
            Schema::table('payroll_deductions', function (Blueprint $table) {
                $table->string('accounting_treatment', 30)->nullable();
            });
        }
        if (!Schema::hasColumn('payroll_deductions', 'deduction_source')) {
            Schema::table('payroll_deductions', function (Blueprint $table) {
                $table->string('deduction_source', 20)->nullable();
            });
        }

        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'employer_sss')) {
                $table->decimal('employer_sss', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'employer_pagibig')) {
                $table->decimal('employer_pagibig', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'employer_philhealth')) {
                $table->decimal('employer_philhealth', 12, 2)->nullable();
            }
        });

        $now = now();
        $contraAccount = DB::table('chart_of_accounts')
            ->where('account_code', '5060')
            ->first();

        if ($contraAccount) {
            DB::table('chart_of_accounts')
                ->where('id', $contraAccount->id)
                ->update([
                    'is_contra' => true,
                    'description' => 'Contra-expense for valid employee wage reductions; do not use for statutory withholdings or employee receivable recoveries.',
                    'updated_at' => $now,
                ]);
        }

        $payable = DB::table('chart_of_accounts')
            ->where('account_code', '2070')
            ->first();

        if ($payable && $payable->account_name !== 'Other Payroll Deductions Payable') {
            throw new RuntimeException('Account code 2070 is already assigned to another account.');
        }

        if (!$payable) {
            DB::table('chart_of_accounts')->insert([
                'account_code' => '2070',
                'account_name' => 'Other Payroll Deductions Payable',
                'account_type' => 'Liability',
                'description' => 'Employee payroll deductions withheld for remittance to third parties',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $payableId = DB::table('chart_of_accounts')
            ->where('account_code', '2070')
            ->value('id');

        DB::table('account_mappings')->insertOrIgnore([
            'mapping_key' => 'other_payroll_deductions_payable',
            'label' => 'Other Payroll Deductions Payable',
            'account_id' => $payableId,
            'updated_at' => $now,
            'created_at' => $now,
        ]);

        $employerExpenseId = DB::table('chart_of_accounts')
            ->where('account_code', '5050')
            ->value('id');

        if ($employerExpenseId) {
            DB::table('account_mappings')->insertOrIgnore([
                'mapping_key' => 'employer_contributions_expense',
                'label' => 'Employer Government Contributions Expense',
                'account_id' => $employerExpenseId,
                'updated_at' => $now,
                'created_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Keep accounting classifications and account mappings to avoid data loss.
    }
};
