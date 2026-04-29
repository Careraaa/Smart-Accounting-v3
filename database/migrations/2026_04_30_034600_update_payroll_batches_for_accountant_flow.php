<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize legacy status values before tightening enum.
        DB::table('payroll_batches')
            ->where('status', 'finalized')
            ->update(['status' => 'submitted']);

        // Expand enum for the new workflow (MySQL).
        // Note: Laravel can't alter enum columns portably without doctrine/dbal.
        try {
            DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','submitted','approved','rejected','paid') NOT NULL DEFAULT 'draft'");
        } catch (\Throwable $e) {
            // If the driver doesn't support this (or already applied), continue.
        }

        Schema::table('payroll_batches', function (Blueprint $table) {
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

            // Foreign keys (added only if not already present).
            // Use nullOnDelete to keep history even if user removed.
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('paid_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payroll_batches', function (Blueprint $table) {
            foreach (['approved_by', 'rejected_by', 'paid_by'] as $fk) {
                try {
                    $table->dropForeign([$fk]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            foreach (['approved_at', 'approved_by', 'rejected_at', 'rejected_by', 'rejection_note', 'paid_at', 'paid_by'] as $col) {
                if (Schema::hasColumn('payroll_batches', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Best-effort revert enum to the original set (MySQL).
        try {
            DB::statement("ALTER TABLE `payroll_batches` MODIFY `status` ENUM('draft','finalized','submitted','paid') NOT NULL DEFAULT 'draft'");
        } catch (\Throwable $e) {
            // ignore
        }
    }
};

