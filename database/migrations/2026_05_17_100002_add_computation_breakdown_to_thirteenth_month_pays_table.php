<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('thirteenth_month_pays')) {
            return;
        }

        if (! Schema::hasColumn('thirteenth_month_pays', 'computation_breakdown')) {
            Schema::table('thirteenth_month_pays', function (Blueprint $table) {
                $table->json('computation_breakdown')->nullable()->after('computed_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('thirteenth_month_pays', 'computation_breakdown')) {
            Schema::table('thirteenth_month_pays', function (Blueprint $table) {
                $table->dropColumn('computation_breakdown');
            });
        }
    }
};
