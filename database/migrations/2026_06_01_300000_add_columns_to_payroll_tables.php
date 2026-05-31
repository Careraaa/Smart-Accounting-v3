<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- payroll_batches table ---
        Schema::table('payroll_batches', function (Blueprint $table) {
            // Drop unique constraint
            try {
                $table->dropUnique('payroll_batches_period_start_period_end_unique');
            } catch (\Throwable $e) {
                // may not exist
            }

            // Add approval/rejection/payment tracking columns
            if (!Schema::hasColumn('payroll_batches', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('finalized_at');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('payroll_batches', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
                $table->text('rejection_note')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('payroll_batches', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('rejection_note');
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }

            // Foreign keys
            try { $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete(); } catch (\Throwable $e) {}
            try { $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete(); } catch (\Throwable $e) {}
            try { $table->foreign('paid_by')->references('id')->on('users')->nullOnDelete(); } catch (\Throwable $e) {}
        });

        // Status enum: final state ('draft','submitted','approved','rejected','paid')
        try {
            DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','submitted','approved','rejected','paid') NOT NULL DEFAULT 'draft'");
        } catch (\Throwable $e) {}

        // Add batch_id to payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('id');
                $table->foreign('batch_id')->references('id')->on('payroll_batches')->nullOnDelete();
            }
        });

        // --- payrolls table ---
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'philhealth')) {
                $table->decimal('philhealth', 10, 2)->default(0)->after('pagibig');
            }
            if (!Schema::hasColumn('payrolls', 'gross_pay')) {
                $table->decimal('gross_pay', 12, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('payrolls', 'net_pay')) {
                $table->decimal('net_pay', 12, 2)->default(0)->after('gross_pay');
            }
            if (!Schema::hasColumn('payrolls', 'withholding_tax')) {
                $table->decimal('withholding_tax', 12, 2)->default(0)->after('philhealth');
            }
            if (!Schema::hasColumn('payrolls', 'total_bonuses')) {
                $table->decimal('total_bonuses', 12, 2)->default(0)->after('total_allowances');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_pay')) {
                $table->decimal('holiday_pay', 12, 2)->default(0)->after('gross_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_ot_pay')) {
                $table->decimal('holiday_ot_pay', 12, 2)->default(0)->after('holiday_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_ot_hours')) {
                $table->decimal('holiday_ot_hours', 8, 2)->default(0)->after('holiday_ot_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_breakdown')) {
                $table->json('holiday_breakdown')->nullable()->after('holiday_ot_hours');
            }
            if (!Schema::hasColumn('payrolls', 'cash_advance_deduction')) {
                $table->decimal('cash_advance_deduction', 12, 2)->default(0)->after('total_deductions');
            }
            if (!Schema::hasColumn('payrolls', 'salary_loan_deduction')) {
                $table->decimal('salary_loan_deduction', 12, 2)->default(0)->after('cash_advance_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'loan_deduction_data')) {
                $table->json('loan_deduction_data')->nullable()->after('salary_loan_deduction');
            }
        });

        // Change days_worked to decimal
        try {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->decimal('days_worked', 8, 2)->default(0)->change();
            });
        } catch (\Throwable $e) {
            // may fail if already decimal
        }

        // --- payroll_allowances ---
        Schema::table('payroll_allowances', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_allowances', 'hours')) {
                $table->decimal('hours', 8, 2)->nullable()->after('allowance_type');
            }
        });

        // --- payroll_deductions ---
        Schema::table('payroll_deductions', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_deductions', 'hours')) {
                $table->decimal('hours', 8, 2)->nullable()->after('deduction_type');
            }
        });
    }

    public function down(): void
    {
        // Payroll deductions
        Schema::table('payroll_deductions', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_deductions', 'hours')) {
                $table->dropColumn('hours');
            }
        });

        // Payroll allowances
        Schema::table('payroll_allowances', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_allowances', 'hours')) {
                $table->dropColumn('hours');
            }
        });

        // Payrolls: revert days_worked to integer
        try {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->integer('days_worked')->default(0)->change();
            });
        } catch (\Throwable $e) {}

        // Payrolls: drop columns
        Schema::table('payrolls', function (Blueprint $table) {
            $cols = ['loan_deduction_data', 'salary_loan_deduction', 'cash_advance_deduction',
                     'holiday_breakdown', 'holiday_ot_hours', 'holiday_ot_pay', 'holiday_pay',
                     'total_bonuses', 'withholding_tax', 'net_pay', 'gross_pay', 'philhealth'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payrolls', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Remove batch_id from payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'batch_id')) {
                try { $table->dropForeign(['batch_id']); } catch (\Throwable $e) {}
                $table->dropColumn('batch_id');
            }
        });

        // Payroll batches: revert status and drop columns
        try {
            DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','finalized','submitted','paid') NOT NULL DEFAULT 'draft'");
        } catch (\Throwable $e) {}

        Schema::table('payroll_batches', function (Blueprint $table) {
            foreach (['approved_by', 'rejected_by', 'paid_by'] as $fk) {
                try { $table->dropForeign([$fk]); } catch (\Throwable $e) {}
            }
            $cols = ['paid_at', 'paid_by', 'rejection_note', 'rejected_at', 'rejected_by',
                     'approved_at', 'approved_by'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payroll_batches', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Restore unique constraint
        Schema::table('payroll_batches', function (Blueprint $table) {
            try {
                $table->unique(['period_start', 'period_end'], 'payroll_batches_period_start_period_end_unique');
            } catch (\Throwable $e) {}
        });
    }
};
