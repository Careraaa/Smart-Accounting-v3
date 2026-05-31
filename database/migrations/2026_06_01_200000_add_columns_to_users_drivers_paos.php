<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- users table ---
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password_changed')) {
                $table->boolean('password_changed')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable()->after('civil_status');
            }
            if (Schema::hasColumn('users', 'session_token')) {
                $table->dropColumn('session_token');
            }
            if (!Schema::hasColumn('users', 'is_logged_in')) {
                $table->boolean('is_logged_in')->default(false)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'has_philhealth')) {
                $table->boolean('has_philhealth')->default(false)->after('has_pagibig');
            }
            if (!Schema::hasColumn('users', 'philhealth_number')) {
                $table->string('philhealth_number')->nullable()->after('pagibig_number');
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('salary_rate');
            }
            if (!Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('users', 'schedule_type')) {
                $table->string('schedule_type')->nullable()->after('bank_account_number');
            }
        });

        // --- drivers table ---
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'gender')) {
                $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable()->after('contact_number');
            }
        });

        // --- paos table ---
        Schema::table('paos', function (Blueprint $table) {
            if (!Schema::hasColumn('paos', 'gender')) {
                $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable()->after('contact_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drops = ['password_changed', 'gender', 'is_logged_in', 'last_login_at',
                      'has_philhealth', 'philhealth_number',
                      'bank_name', 'bank_account_number', 'schedule_type'];
            foreach ($drops as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'gender')) {
                $table->dropColumn('gender');
            }
        });

        Schema::table('paos', function (Blueprint $table) {
            if (Schema::hasColumn('paos', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
