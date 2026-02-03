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
        Schema::create('fare_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_remittance_id')->constrained('daily_remittances')->onDelete('cascade');
            $table->integer('passenger_count')->nullable();
            $table->decimal('fare_amount', 10, 2);
            $table->dateTime('collection_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_collections');
    }
};
