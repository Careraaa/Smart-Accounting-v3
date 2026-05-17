<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the thirteenth_month_pays table to store 13th month pay computations
     * per employee for each calendar year.
     */
    public function up(): void
    {
        Schema::create('thirteenth_month_pays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->year('calendar_year');
            
            // Computation tracking
            $table->decimal('total_basic_salary_earned', 12, 2)->default(0)->comment('Total basic salary earned within the calendar year');
            $table->decimal('thirteenth_month_pay', 12, 2)->default(0)->comment('Computed 13th month pay = total_basic_salary_earned / 12');
            
            // Eligibility tracking
            $table->integer('months_worked')->default(0)->comment('Number of months with at least one day of service');
            $table->boolean('is_eligible')->default(false)->comment('Eligible if at least 1 month of service within the calendar year');
            
            // Payment tracking
            $table->decimal('amount_paid', 12, 2)->default(0)->comment('Amount already paid to the employee');
            $table->decimal('amount_remaining', 12, 2)->default(0)->comment('Remaining amount to be paid');
            $table->string('status')->default('pending')->comment('pending, partial, paid'); // pending, partial, paid
            $table->date('payment_date')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Computation details and metadata
            $table->text('notes')->nullable();
            $table->foreignId('computed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('computed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: one record per employee per calendar year
            $table->unique(['user_id', 'calendar_year']);
            
            // Indexes for efficient queries
            $table->index(['calendar_year', 'status']);
            $table->index(['is_eligible', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thirteenth_month_pays');
    }
};
