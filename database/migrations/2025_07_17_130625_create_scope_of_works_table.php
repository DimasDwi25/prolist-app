<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scope_of_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phc_id')
                ->constrained('phcs')
                ->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('items')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scope_of_works');
    }
};
