<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // -------------------------
        // SYSTEM TABLES
        // -------------------------

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('employee');
            $table->string('profile_picture')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });

        // -------------------------
        // TRANSPORT TABLES
        // -------------------------

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_number')->unique();
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->date('date_of_hire')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('paos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('conductor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->date('date_of_hire');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('origin');
            $table->string('destination');
            $table->decimal('distance', 8, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->string('operator');
            $table->string('vehicle_type')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('status')->default('active');
            $table->date('date_purchased')->nullable();
            $table->timestamps();
        });

        Schema::create('daily_remittances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('pao_id')->constrained('paos')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('remittance_date');
            $table->decimal('total_collection', 10, 2)->default(0);
            $table->decimal('total_expenses', 10, 2)->default(0);
            $table->decimal('net_remittance', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('fare_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_remittance_id')->constrained('daily_remittances')->onDelete('cascade');
            $table->integer('passenger_count')->default(0);
            $table->decimal('fare_amount', 10, 2)->default(0);
            $table->datetime('collection_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_remittance_id')->constrained('daily_remittances')->onDelete('cascade');
            $table->string('expense_type');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expense_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // EMPLOYEE TABLES
        // -------------------------

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('educational_attainment')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->date('driver_license_validity')->nullable();
            $table->date('date_of_hire');
            $table->string('position');
            $table->string('department')->nullable();
            $table->string('status')->default('active');
            $table->decimal('salary_rate', 10, 2)->default(0);
            $table->boolean('has_sss')->default(false);
            $table->string('sss_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->boolean('has_tin')->default(false);
            $table->string('pagibig_number')->nullable();
            $table->boolean('has_pagibig')->default(false);
            $table->string('signature_path')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position')->nullable();
            $table->string('duration')->nullable();
            $table->text('responsibilities')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('skill_name');
            $table->string('proficiency')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('relationship')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('attachment_type');
            $table->string('file_path')->nullable();
            $table->boolean('provided')->default(false);
            $table->timestamps();
        });

        // -------------------------
        // ATTENDANCE TABLES
        // -------------------------

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->date('date');
            $table->string('qr_code')->nullable();
            $table->string('status')->default('present');
            $table->timestamps();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['time_in', 'time_out']);
            $table->datetime('logged_at');
            $table->timestamps();
        });

        Schema::create('attendance_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->boolean('used')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // -------------------------
        // HR / PAYROLL TABLES
        // -------------------------

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('overtime_undertimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->string('type');
            $table->decimal('hours', 5, 2);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('statutory_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->decimal('employee_share', 12, 2)->nullable();
            $table->decimal('employer_share', 12, 2)->nullable();
            $table->decimal('percentage_employee', 5, 2)->nullable();
            $table->decimal('percentage_employer', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->decimal('total_allowances', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->date('payment_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->string('allowance_type');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->string('deduction_type');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('payroll_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->string('allowance_type');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->string('deduction_type');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('salary_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('loan_amount', 10, 2)->default(0);
            $table->decimal('monthly_deduction', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('request_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop in reverse order to respect foreign keys
        Schema::dropIfExists('cash_advances');
        Schema::dropIfExists('salary_loans');
        Schema::dropIfExists('payroll_deductions');
        Schema::dropIfExists('payroll_allowances');
        Schema::dropIfExists('deductions');
        Schema::dropIfExists('allowances');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('statutory_deductions');
        Schema::dropIfExists('overtime_undertimes');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('attendance_tokens');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employee_attachments');
        Schema::dropIfExists('employee_references');
        Schema::dropIfExists('employee_beneficiaries');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('trip_expenses');
        Schema::dropIfExists('fare_collections');
        Schema::dropIfExists('daily_remittances');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('paos');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
