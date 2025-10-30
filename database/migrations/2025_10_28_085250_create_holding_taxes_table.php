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
        Schema::create('holding_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->decimal('pph23_rate', 8, 4)->nullable();
            $table->decimal('nilai_pph23', 18, 2)->nullable();
            $table->decimal('pph42_rate', 8, 4)->nullable();
            $table->decimal('nilai_pph42', 18, 2)->nullable();
            $table->string('no_bukti_potong')->nullable();
            $table->decimal('nilai_potongan', 18, 2)->nullable();
            $table->date('tanggal_wht')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holding_taxes');
    }
};
