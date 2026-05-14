<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the old session_token column if it exists, replace with a simple boolean flag
            if (Schema::hasColumn('users', 'session_token')) {
                $table->dropColumn('session_token');
            }
            $table->boolean('is_logged_in')->default(false)->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_logged_in');
        });
    }
};
