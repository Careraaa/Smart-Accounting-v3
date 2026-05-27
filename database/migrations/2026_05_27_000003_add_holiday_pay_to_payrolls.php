<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'holiday_pay')) {
                $table->decimal('holiday_pay', 12, 2)->default(0)->after('gross_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_ot_pay')) {
                $table->decimal('holiday_ot_pay', 12, 2)->default(0)->after('holiday_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_ot_hours')) {
                $table->decimal('holiday_ot_hours', 8, 2)->default(0)->after('holiday_ot_pay');
            }
            if (!Schema::hasColumn('payrolls', 'holiday_breakdown')) {
                $table->json('holiday_breakdown')->nullable()->after('holiday_ot_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'holiday_breakdown')) {
                $table->dropColumn('holiday_breakdown');
            }
            if (Schema::hasColumn('payrolls', 'holiday_ot_hours')) {
                $table->dropColumn('holiday_ot_hours');
            }
            if (Schema::hasColumn('payrolls', 'holiday_ot_pay')) {
                $table->dropColumn('holiday_ot_pay');
            }
            if (Schema::hasColumn('payrolls', 'holiday_pay')) {
                $table->dropColumn('holiday_pay');
            }
        });
    }
};
