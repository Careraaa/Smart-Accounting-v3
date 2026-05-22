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
        Schema::table('shifts', function (Blueprint $table) {
            // Add break start and end times
            $table->time('break_start')->nullable()->after('end_time');
            $table->time('break_end')->nullable()->after('break_start');
            
            // Drop the old break_duration column
            $table->dropColumn('break_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Add back break_duration
            $table->time('break_duration')->nullable();
            
            // Drop the new columns
            $table->dropColumn(['break_start', 'break_end']);
        });
    }
};
