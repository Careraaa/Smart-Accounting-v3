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
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('gl_account_id');
            $table->decimal('debit_amount', 19, 2)->default(0);
            $table->decimal('credit_amount', 19, 2)->default(0);
            $table->text('line_description')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable(); // For expense allocation
            $table->string('reference_detail')->nullable(); // Additional line-level reference
            $table->integer('line_number');
            $table->timestamps();
            
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts');
            $table->index('gl_account_id');
            $table->index(['journal_entry_id', 'gl_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
