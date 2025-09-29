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
            $table->foreignId('project_id')
                ->constrained('projects', 'pn_number')
                ->noActionOnDelete();
            $table->text('work_details');
            $table->foreignId('pic')->nullable()->constrained('users', 'id');
            $table->dateTime('target_finish_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('finish_date')->nullable();
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
