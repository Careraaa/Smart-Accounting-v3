<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('boundary_payments', 'paid_by')) {
            Schema::table('boundary_payments', function (Blueprint $table) {
                $table->string('paid_by', 16)->nullable();
            });
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `boundary_payments` MODIFY COLUMN `paid_by` VARCHAR(16) NULL');
        }
    }

    public function down(): void
    {
        // Keep the partner-allocation history intact when rolling back.
    }
};
