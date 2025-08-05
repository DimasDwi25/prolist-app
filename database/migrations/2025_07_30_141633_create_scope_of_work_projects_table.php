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
        Schema::create('scope_of_work_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scope_of_work_id')->constrained('scope_of_works')->noActionOnDelete();
            $table->foreignId('project_id')->constrained('projects', 'pn_number')->noActionOnDelete();
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scope_of_work_projects');
    }
};
