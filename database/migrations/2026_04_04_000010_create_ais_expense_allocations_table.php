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
        Schema::create('expense_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_line_id');
            $table->unsignedBigInteger('cost_center_id');
            $table->decimal('allocated_amount', 19, 2);
            $table->decimal('allocation_percentage', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('journal_entry_line_id')->references('id')->on('journal_entry_lines')->onDelete('cascade');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
            $table->index('cost_center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_allocations');
    }
};
