<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- leaves table ---
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leaves', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->nullable()->after('user_id')->constrained('leave_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('leaves', 'paid_days')) {
                $table->integer('paid_days')->default(0)->after('status');
            }
            if (!Schema::hasColumn('leaves', 'unpaid_days')) {
                $table->integer('unpaid_days')->default(0)->after('paid_days');
            }
        });

        // Add 'paid' status to leaves status enum
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE `leaves` MODIFY `status` ENUM('pending', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'pending'");
            } catch (\Throwable $e) {}
        }

        // --- cash_advances table ---
        Schema::table('cash_advances', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_advances', 'repayment_months')) {
                $table->integer('repayment_months')->default(1)->after('amount');
            }
            if (!Schema::hasColumn('cash_advances', 'monthly_deduction')) {
                $table->decimal('monthly_deduction', 12, 2)->default(0)->after('repayment_months');
            }
            if (!Schema::hasColumn('cash_advances', 'amount_deducted')) {
                $table->decimal('amount_deducted', 12, 2)->default(0)->after('monthly_deduction');
            }
        });

        // --- salary_loans table ---
        Schema::table('salary_loans', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_loans', 'total_months')) {
                $table->integer('total_months')->default(12)->after('loan_amount');
            }
        });

        // --- attendance table ---
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE `attendance` MODIFY `status` ENUM('present', 'late', 'absent', 'on leave') NULL");
            } catch (\Throwable $e) {}
        }

        // --- overtime_undertimes table ---
        Schema::table('overtime_undertimes', function (Blueprint $table) {
            if (!Schema::hasColumn('overtime_undertimes', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('overtime_undertimes', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('rejection_reason');
            }
        });

        // --- notifications table ---
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->index()->after('read_at');
            }
        });
    }

    public function down(): void
    {
        // Notifications
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });

        // Overtime undertimes
        Schema::table('overtime_undertimes', function (Blueprint $table) {
            try { $table->dropForeign(['approved_by']); } catch (\Throwable $e) {}
            $drops = [];
            if (Schema::hasColumn('overtime_undertimes', 'approved_by')) $drops[] = 'approved_by';
            if (Schema::hasColumn('overtime_undertimes', 'rejection_reason')) $drops[] = 'rejection_reason';
            if ($drops) $table->dropColumn($drops);
        });

        // Attendance
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE `attendance` MODIFY `status` ENUM('present', 'late', 'absent') NULL");
            } catch (\Throwable $e) {}
        }

        // Salary loans
        Schema::table('salary_loans', function (Blueprint $table) {
            if (Schema::hasColumn('salary_loans', 'total_months')) {
                $table->dropColumn('total_months');
            }
        });

        // Cash advances
        Schema::table('cash_advances', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('cash_advances', 'amount_deducted')) $drops[] = 'amount_deducted';
            if (Schema::hasColumn('cash_advances', 'monthly_deduction')) $drops[] = 'monthly_deduction';
            if (Schema::hasColumn('cash_advances', 'repayment_months')) $drops[] = 'repayment_months';
            if ($drops) $table->dropColumn($drops);
        });

        // Leaves
        Schema::table('leaves', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                try {
                    DB::statement("ALTER TABLE `leaves` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
                } catch (\Throwable $e) {}
            }
            try { $table->dropForeign(['leave_type_id']); } catch (\Throwable $e) {}
            $drops = [];
            if (Schema::hasColumn('leaves', 'unpaid_days')) $drops[] = 'unpaid_days';
            if (Schema::hasColumn('leaves', 'paid_days')) $drops[] = 'paid_days';
            if (Schema::hasColumn('leaves', 'leave_type_id')) $drops[] = 'leave_type_id';
            if (Schema::hasColumn('leaves', 'rejection_reason')) $drops[] = 'rejection_reason';
            if ($drops) $table->dropColumn($drops);
        });
    }
};
