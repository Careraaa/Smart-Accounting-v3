<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'work_days_per_week')) {
                $table->tinyInteger('work_days_per_week')->default(5)->after('salary_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'work_days_per_week')) {
                $table->dropColumn('work_days_per_week');
            }
        });
    }
};
