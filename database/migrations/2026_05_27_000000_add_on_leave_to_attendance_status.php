<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'on leave' to attendance status enum
        // Using raw SQL to handle enum modification across different databases

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `attendance` MODIFY `status` ENUM('present', 'late', 'absent', 'on leave') NULL");
        } else {
            // For other databases, you might need a different approach
            Schema::table('attendance', function (Blueprint $table) {
                // Fallback: this is database-specific and may need adjustment
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `attendance` MODIFY `status` ENUM('present', 'late', 'absent') NULL");
        }
    }
};
