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
        // Add has_philhealth and philhealth_number to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_philhealth')->default(false)->after('has_pagibig');
            $table->string('philhealth_number')->nullable()->after('pagibig_number');
        });

        // Add philhealth to payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('philhealth', 10, 2)->default(0)->after('pagibig');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_philhealth');
            $table->dropColumn('philhealth_number');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('philhealth');
        });
    }
};
