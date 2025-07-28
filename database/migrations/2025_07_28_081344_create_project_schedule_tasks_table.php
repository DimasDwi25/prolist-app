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
        Schema::create('project_schedule_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete(); // dari master task
            $table->integer('quantity')->default(1);
            $table->string('unit', 50)->nullable(); // jika tidak ada di master, bisa nullable
            $table->decimal('bobot', 5, 2)->default(0);
            $table->date('plan_start');
            $table->date('plan_finish');
            $table->date('actual_start')->nullable();
            $table->date('actual_finish')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_schedule_tasks');
    }
};
