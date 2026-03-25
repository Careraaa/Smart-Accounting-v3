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
            // User Authentication
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role')->default('employee');
            $table->string('profile_picture')->nullable();
            $table->rememberToken();

            // Employee Information
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('educational_attainment')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->date('driver_license_validity')->nullable();
            $table->date('date_of_hire')->nullable();
            $table->string('position')->nullable();
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
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->string('operator');
            $table->string('status')->default('active');
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

        // -------------------------
        // EMPLOYEE RELATED TABLES
        // -------------------------

        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position')->nullable();
            $table->string('duration')->nullable();
            $table->text('responsibilities')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('skill_name');
            $table->string('proficiency')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('relationship')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('attachment_key'); // e.g. 'drivers_license'
            $table->string('label'); // e.g. "Driver's License"
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->enum('uploaded_by_role', ['hr', 'employee'])->default('hr');
            $table->timestamps();
            $table->index(['user_id', 'attachment_key']);
        });

        // -------------------------
        // ATTENDANCE TABLES
        // -------------------------

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent'])->nullable();
            $table->boolean('is_manual')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->string('type'); 
            $table->decimal('hours', 5, 2);

            
            $table->decimal('amount', 15, 2)->nullable(); 
            $table->decimal('hourly_rate_used', 15, 2)->nullable(); 

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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->decimal('total_allowances', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->integer('days_worked')->default(0);
            $table->decimal('hours_worked', 8, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->string('allowance_type');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('loan_amount', 10, 2)->default(0);
            $table->decimal('monthly_deduction', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('months_paid')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('request_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('deducted_payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // NOTIFICATIONS TABLE
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // e.g., 'leave_approved', 'overtime_submitted', 'payroll_processed'
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data like related IDs
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        // Drop in reverse order to respect foreign keys
        Schema::dropIfExists('notifications');
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
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('employee_attachments');
        Schema::dropIfExists('employee_references');
        Schema::dropIfExists('employee_beneficiaries');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('daily_remittances');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('paos');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
