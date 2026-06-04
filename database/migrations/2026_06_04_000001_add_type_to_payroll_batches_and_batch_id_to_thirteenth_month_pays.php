<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_batches', 'type')) {
                $table->string('type', 50)->default('regular')->after('id');
            }
            $table->index('type');
        });

        Schema::table('thirteenth_month_pays', function (Blueprint $table) {
            if (!Schema::hasColumn('thirteenth_month_pays', 'batch_id')) {
                $table->foreignId('batch_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('payroll_batches')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('thirteenth_month_pays', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });

        Schema::table('payroll_batches', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
