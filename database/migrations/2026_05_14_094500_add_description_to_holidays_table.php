<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('holidays') && !Schema::hasColumn('holidays', 'description')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->text('description')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('holidays') && Schema::hasColumn('holidays', 'description')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
