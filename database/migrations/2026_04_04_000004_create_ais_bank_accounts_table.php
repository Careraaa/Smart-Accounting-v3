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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 255);
            $table->string('account_number', 50)->unique();
            $table->string('account_holder', 255);
            $table->string('branch_code', 50)->nullable();
            $table->string('swift_code', 20)->nullable();
            $table->string('currency', 3)->default('PHP');
            $table->enum('account_type', ['Checking', 'Savings', 'Operating']);
            $table->unsignedBigInteger('gl_account_id');
            $table->boolean('is_active')->default(true);
            $table->decimal('opening_balance', 19, 2)->default(0);
            $table->decimal('current_balance', 19, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts');
            $table->index('is_active');
            $table->index('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
