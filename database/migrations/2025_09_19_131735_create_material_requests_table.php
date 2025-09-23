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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pn_id')
                ->nullable()
                ->constrained('projects', 'pn_number')
                ->noActionOnDelete();
            $table->integer('material_number');
            $table->text('material_description');
            $table->dateTime('material_created');
            $table->foreignId('created_by')->constrained('users');
            $table->dateTime('target_date');
            $table->dateTime('cancel_date')->nullable();
            $table->dateTime('complete_date')->nullable();
            $table->enum('material_status', [
                'On Progress',
                'Canceled',
                'Hold',
                'Delayed',
                'Completed'
            ]);
            $table->boolean('additional_material')->nullable();
            $table->foreignId('material_handover')->nullable()->constrained('users');
            $table->dateTime('ho_date')->nullable();
            $table->text('remark');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
