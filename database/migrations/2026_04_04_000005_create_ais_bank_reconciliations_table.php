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
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->date('statement_date');
            $table->decimal('bank_balance', 19, 2);
            $table->decimal('book_balance', 19, 2);
            $table->decimal('difference', 19, 2)->default(0);
            $table->enum('status', ['Draft', 'In Progress', 'Completed', 'Verified']);
            $table->decimal('total_deposits_in_transit', 19, 2)->default(0);
            $table->decimal('total_outstanding_checks', 19, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['bank_account_id', 'statement_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
