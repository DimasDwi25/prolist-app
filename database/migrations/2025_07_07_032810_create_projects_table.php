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
        Schema::create('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('pn_number')->primary();
            $table->string('project_name');
            $table->string('project_number');
            $table->foreignId('categories_project_id')
                ->constrained('project_categories')
                ->onDelete('cascade');
            $table->foreignId('quotations_id')
                ->constrained('quotations')
                ->onDelete('cascade');
            $table->dateTime('phc_dates')->nullable();
            $table->integer('mandays_engineer')->nullable();
            $table->integer('mandays_technician')->nullable();
            $table->dateTime('target_dates')->nullable();
            $table->enum('material_status', ['ready', 'not ready'])->nullable();
            $table->dateTime('dokumen_finish_date')->nullable();
            $table->dateTime('engineering_finish_date')->nullable();
            $table->decimal('jumlah_invoice', 15, 2)->nullable();
            $table->foreignId('status_project_id')
                ->constrained('status_projects')
                ->onDelete('cascade')->nullable();
            $table->string('project_progress')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
