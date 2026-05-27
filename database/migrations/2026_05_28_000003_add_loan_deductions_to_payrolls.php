<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('cash_advance_deduction', 12, 2)->default(0)->after('total_deductions');
            $table->decimal('salary_loan_deduction', 12, 2)->default(0)->after('cash_advance_deduction');
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['cash_advance_deduction', 'salary_loan_deduction']);
        });
    }
};
