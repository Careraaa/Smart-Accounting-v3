<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable()->after('contact_number');
        });

        Schema::table('paos', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'prefer_not_to_say'])->nullable()->after('contact_number');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('paos', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
