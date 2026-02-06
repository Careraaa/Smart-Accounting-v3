<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('basic_salary', 10, 2)->default(0)->after('payroll_period_end');
            $table->decimal('net_salary', 10, 2)->default(0)->after('total_deductions');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'net_salary']);
        });
    }
};
