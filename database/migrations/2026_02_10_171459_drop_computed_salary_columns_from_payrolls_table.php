<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'basic_salary')) {
                $table->dropColumn('basic_salary');
            }

            if (Schema::hasColumn('payrolls', 'net_salary')) {
                $table->dropColumn('net_salary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->decimal('net_salary', 12, 2)->nullable();
        });
    }
};
