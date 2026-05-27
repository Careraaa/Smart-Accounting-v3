<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('salary_loans', function (Blueprint $table) {
            $table->integer('total_months')->default(12)->after('loan_amount');
        });
    }

    public function down()
    {
        Schema::table('salary_loans', function (Blueprint $table) {
            $table->dropColumn('total_months');
        });
    }
};
