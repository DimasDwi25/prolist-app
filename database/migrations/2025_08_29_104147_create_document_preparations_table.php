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
        Schema::create('document_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents_phc');
            $table->foreignId('phc_id')->constrained('phcs');
            $table->boolean('is_applicable');
            $table->dateTime('date_prepared')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_preparations');
    }
};
