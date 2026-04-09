<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'gross_pay')) {
                $table->decimal('gross_pay', 12, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('payrolls', 'net_pay')) {
                $table->decimal('net_pay', 12, 2)->default(0)->after('gross_pay');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'net_pay')) {
                $table->dropColumn('net_pay');
            }
            if (Schema::hasColumn('payrolls', 'gross_pay')) {
                $table->dropColumn('gross_pay');
            }
        });
    }
};

