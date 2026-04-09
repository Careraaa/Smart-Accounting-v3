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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code', 50)->unique();
            $table->string('supplier_name', 255);
            $table->string('contact_person', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->enum('payment_terms', ['COD', 'Net 7', 'Net 15', 'Net 30', 'Net 60', 'Custom'])->default('Net 30');
            $table->text('payment_terms_description')->nullable();
            $table->string('bank_account_name', 255)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->decimal('credit_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
