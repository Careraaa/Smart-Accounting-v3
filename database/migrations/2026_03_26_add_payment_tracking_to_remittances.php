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
        Schema::table('daily_remittances', function (Blueprint $table) {
            // Add payment tracking columns for short remittance resolution
            $table->decimal('driver_amount_paid', 10, 2)->nullable()->default(0)->after('driver_share');
            $table->enum('driver_status', ['pending', 'partial', 'paid'])->nullable()->default('pending')->after('driver_amount_paid');
            $table->decimal('pao_amount_paid', 10, 2)->nullable()->default(0)->after('pao_share');
            $table->enum('pao_status', ['pending', 'partial', 'paid'])->nullable()->default('pending')->after('pao_amount_paid');
            $table->text('resolution_notes')->nullable()->after('pao_status');
            $table->timestamp('resolved_at')->nullable()->after('resolution_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $table->dropColumn([
                'driver_amount_paid',
                'driver_status',
                'pao_amount_paid',
                'pao_status',
                'resolution_notes',
                'resolved_at',
            ]);
        });
    }
};
