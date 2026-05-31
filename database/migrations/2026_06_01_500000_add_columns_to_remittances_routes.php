<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- routes table ---
        Schema::table('routes', function (Blueprint $table) {
            if (!Schema::hasColumn('routes', 'boundary')) {
                $table->decimal('boundary', 10, 2)->nullable()->after('route_name');
            }
        });

        // --- daily_remittances table ---
        Schema::table('daily_remittances', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_remittances', 'boundary')) {
                $table->decimal('boundary', 10, 2)->nullable()->after('total_expenses');
            }
            if (!Schema::hasColumn('daily_remittances', 'is_short_remittance')) {
                $table->boolean('is_short_remittance')->default(false)->after('net_remittance');
            }
            if (!Schema::hasColumn('daily_remittances', 'short_amount')) {
                $table->decimal('short_amount', 10, 2)->nullable()->after('is_short_remittance');
            }
            if (!Schema::hasColumn('daily_remittances', 'driver_share')) {
                $table->decimal('driver_share', 10, 2)->nullable()->after('short_amount');
            }
            if (!Schema::hasColumn('daily_remittances', 'pao_share')) {
                $table->decimal('pao_share', 10, 2)->nullable()->after('driver_share');
            }
            if (!Schema::hasColumn('daily_remittances', 'driver_amount_paid')) {
                $table->decimal('driver_amount_paid', 10, 2)->nullable()->default(0)->after('driver_share');
            }
            if (!Schema::hasColumn('daily_remittances', 'driver_status')) {
                $table->enum('driver_status', ['pending', 'partial', 'paid'])->nullable()->default('pending')->after('driver_amount_paid');
            }
            if (!Schema::hasColumn('daily_remittances', 'pao_amount_paid')) {
                $table->decimal('pao_amount_paid', 10, 2)->nullable()->default(0)->after('pao_share');
            }
            if (!Schema::hasColumn('daily_remittances', 'pao_status')) {
                $table->enum('pao_status', ['pending', 'partial', 'paid'])->nullable()->default('pending')->after('pao_amount_paid');
            }
            if (!Schema::hasColumn('daily_remittances', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('pao_status');
            }
            if (!Schema::hasColumn('daily_remittances', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resolution_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $drops = ['resolved_at', 'resolution_notes', 'pao_status', 'pao_amount_paid',
                      'driver_status', 'driver_amount_paid', 'pao_share', 'driver_share',
                      'short_amount', 'is_short_remittance', 'boundary'];
            $existing = [];
            foreach ($drops as $col) {
                if (Schema::hasColumn('daily_remittances', $col)) {
                    $existing[] = $col;
                }
            }
            if ($existing) $table->dropColumn($existing);
        });

        Schema::table('routes', function (Blueprint $table) {
            if (Schema::hasColumn('routes', 'boundary')) {
                $table->dropColumn('boundary');
            }
        });
    }
};
