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
        Schema::create('request_invoice_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_invoice_id')->constrained('request_invoices')->onDelete('cascade');
            $table->foreignId('document_preparation_id')->constrained('document_preparations')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_invoice_documents');
    }
};
