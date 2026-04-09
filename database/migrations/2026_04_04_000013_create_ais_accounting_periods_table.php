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
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 50);
            $table->integer('fiscal_year');
            $table->integer('period_number'); // 1-12 for monthly
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['Open', 'Closed', 'Locked'])->default('Open');
            $table->boolean('is_current')->default(false);
            $table->integer('number_of_entries')->default(0);
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['fiscal_year', 'period_number']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
