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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->integer('payment_number');
            $table->date('payment_date');
            $table->decimal('payment_amount', 18, 2);
            $table->enum('currency', ['IDR', 'USD'])->default('IDR');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->unique(['invoice_id', 'payment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
