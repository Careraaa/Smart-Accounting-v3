<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->foreignId('payroll_id')->after('employee_id')->nullable()->constrained('payrolls')->onDelete('cascade');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->foreignId('payroll_id')->after('employee_id')->nullable()->constrained('payrolls')->onDelete('cascade');
        });
    }
};
