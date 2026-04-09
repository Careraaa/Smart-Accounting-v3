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
        Schema::create('trial_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_account_id');
            $table->date('as_of_date');
            $table->enum('trial_balance_type', ['Unadjusted', 'Adjusted']);
            $table->decimal('debit_balance', 19, 2)->default(0);
            $table->decimal('credit_balance', 19, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unique(['gl_account_id', 'as_of_date', 'trial_balance_type'], 'tb_acct_date_type_uq');
            $table->index('as_of_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_balances');
    }
};
