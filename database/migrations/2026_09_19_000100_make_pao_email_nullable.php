<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paos', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->date('date_of_hire')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('paos', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->date('date_of_hire')->nullable(false)->change();
        });
    }
};
