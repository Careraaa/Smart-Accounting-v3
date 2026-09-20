<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $table->decimal('diesel', 10, 2)->nullable()->after('total_collection');
            $table->decimal('parking', 10, 2)->nullable()->after('diesel');
            $table->decimal('dispatcher', 10, 2)->nullable()->after('parking');
            $table->decimal('food_allowance', 10, 2)->nullable()->after('dispatcher');
            $table->decimal('barker', 10, 2)->nullable()->after('food_allowance');
            $table->decimal('others', 10, 2)->nullable()->after('barker');
        });
    }

    public function down(): void
    {
        Schema::table('daily_remittances', function (Blueprint $table) {
            $table->dropColumn([
                'diesel',
                'parking',
                'dispatcher',
                'food_allowance',
                'barker',
                'others',
            ]);
        });
    }
};