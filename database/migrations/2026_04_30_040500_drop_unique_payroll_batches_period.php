<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_batches', function (Blueprint $table) {
            // Allow multiple batches for the same period (for repeated testing / regeneration).
            // Index name created by Laravel for unique(['period_start','period_end']) is typically:
            // payroll_batches_period_start_period_end_unique
            $table->dropUnique('payroll_batches_period_start_period_end_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_batches', function (Blueprint $table) {
            $table->unique(['period_start', 'period_end'], 'payroll_batches_period_start_period_end_unique');
        });
    }
};

