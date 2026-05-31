<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- shifts (final schema: break_start/break_end instead of break_duration) ---
        if (!Schema::hasTable('shifts')) {
            Schema::create('shifts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->time('start_time');
                $table->time('end_time');
                $table->time('break_start')->nullable();
                $table->time('break_end')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // --- leave_types ---
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('abbreviation')->nullable();
                $table->integer('days_allowed')->default(0);
                $table->boolean('carry_over')->default(false);
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // --- employee_leave_balances ---
        if (!Schema::hasTable('employee_leave_balances')) {
            Schema::create('employee_leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
                $table->integer('total_days');
                $table->integer('used_days')->default(0);
                $table->integer('remaining_days');
                $table->year('year');
                $table->timestamps();
                $table->unique(['user_id', 'leave_type_id', 'year']);
            });
        }

        // --- payroll_batches (created after base migration) ---
        if (!Schema::hasTable('payroll_batches')) {
            Schema::create('payroll_batches', function (Blueprint $table) {
                $table->id();
                $table->date('period_start');
                $table->date('period_end');
                $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
                $table->unsignedBigInteger('generated_by')->nullable();
                $table->unsignedBigInteger('finalized_by')->nullable();
                $table->timestamp('finalized_at')->nullable();
                $table->timestamps();

                $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('finalized_by')->references('id')->on('users')->nullOnDelete();

                $table->unique(['period_start', 'period_end']);
            });
        }

        // --- holidays ---
        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('date');
                $table->string('type')->nullable();
                $table->timestamps();
            });
        }

        // --- payroll_cutoff_schedules ---
        if (!Schema::hasTable('payroll_cutoff_schedules')) {
            Schema::create('payroll_cutoff_schedules', function (Blueprint $table) {
                $table->id();
                $table->integer('cutoff_day');
                $table->string('label');
                $table->date('payroll_period_start')->nullable();
                $table->date('payroll_period_end')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique('cutoff_day');
            });
        }

        // --- settings ---
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // --- pinned_items ---
        if (!Schema::hasTable('pinned_items')) {
            Schema::create('pinned_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->string('url', 500);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['user_id', 'sort_order']);
                $table->unique(['user_id', 'url']);
            });
        }

        // --- user_activities ---
        if (!Schema::hasTable('user_activities')) {
            Schema::create('user_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('action');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('subject_label');
                $table->string('url')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }

        // --- withholding_taxes ---
        if (!Schema::hasTable('withholding_taxes')) {
            Schema::create('withholding_taxes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('min_salary', 12, 2)->nullable();
                $table->decimal('max_salary', 12, 2)->nullable();
                $table->decimal('employee_share', 12, 2)->nullable();
                $table->decimal('percentage_employee', 5, 2)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // --- bonuses + bonus_employee ---
        if (!Schema::hasTable('bonuses')) {
            Schema::create('bonuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type');
                $table->string('computation_method');
                $table->text('formula')->nullable();
                $table->decimal('fixed_amount', 12, 2)->nullable();
                $table->decimal('percentage_value', 8, 4)->nullable();
                $table->boolean('is_mandatory')->default(false);
                $table->boolean('is_system_generated')->default(false);
                $table->string('status')->default('active');
                $table->unsignedSmallInteger('year')->nullable();
                $table->string('payroll_period')->nullable();
                $table->string('code')->nullable()->unique();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bonus_employee')) {
            Schema::create('bonus_employee', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bonus_id')->constrained('bonuses')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unique(['bonus_id', 'user_id']);
            });
        }

        // --- payroll_bonuses ---
        if (!Schema::hasTable('payroll_bonuses')) {
            Schema::create('payroll_bonuses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
                $table->enum('bonus_type', ['performance', 'holiday', 'attendance', 'special']);
                $table->string('description')->nullable();
                $table->decimal('amount', 12, 2);
                $table->timestamps();
            });
        }

        // --- thirteenth_month_pays (final state with computation_breakdown) ---
        if (!Schema::hasTable('thirteenth_month_pays')) {
            Schema::create('thirteenth_month_pays', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedSmallInteger('calendar_year');
                $table->decimal('total_basic_salary_earned', 14, 2)->default(0);
                $table->decimal('thirteenth_month_pay', 14, 2)->default(0);
                $table->decimal('months_worked', 5, 2)->default(0);
                $table->boolean('is_eligible')->default(true);
                $table->decimal('amount_paid', 14, 2)->default(0);
                $table->decimal('amount_remaining', 14, 2)->default(0);
                $table->string('status')->default('pending');
                $table->date('payment_date')->nullable();
                $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('computed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('computed_at')->nullable();
                $table->json('computation_breakdown')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['user_id', 'calendar_year']);
                $table->index(['calendar_year', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_bonuses');
        Schema::dropIfExists('bonus_employee');
        Schema::dropIfExists('bonuses');
        Schema::dropIfExists('withholding_taxes');
        Schema::dropIfExists('thirteenth_month_pays');
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('pinned_items');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('payroll_cutoff_schedules');
        Schema::dropIfExists('payroll_batches');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('employee_leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('shifts');
    }
};
