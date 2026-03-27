<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_cutoff_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('cutoff_day')->comment('Day of month for cutoff (e.g., 5 or 28)');
            $table->string('label')->comment('Human-readable label (e.g., "5th of the Month")');
            $table->date('payroll_period_start')->nullable()->comment('Start date for this cutoff period');
            $table->date('payroll_period_end')->nullable()->comment('End date for this cutoff period');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('cutoff_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_cutoff_schedules');
    }
};
