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
        Schema::create('project_week_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_task_id')->constrained()->cascadeOnDelete();
            $table->integer('week_number');
            $table->date('week_start')->nullable();
            $table->decimal('bobot_plan', 5, 2)->default(0);
            $table->decimal('bobot_actual', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_week_schedules');
    }
};
