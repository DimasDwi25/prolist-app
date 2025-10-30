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
        Schema::create('phcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects', 'pn_number')
                ->noActionOnDelete();
            $table->foreignId('ho_marketings_id')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();

            $table->foreignId('ho_engineering_id')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('target_finish_date')->nullable();
            $table->string('client_pic_name');
            $table->string('client_mobile')->nullable();
            $table->string('client_reps_office_address')->nullable();
            $table->string('client_site_address')->nullable();
            $table->string('client_site_representatives')->nullable();
            $table->string('site_phone_number')->nullable();
            $table->enum('status', ['pending', 'ready'])->default('pending');
            $table->foreignId('pic_engineering_id')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();

            $table->foreignId('pic_marketing_id')
                ->nullable()
                ->constrained('users')
                ->noActionOnDelete();

            $table->dateTime('handover_date')->nullable();

            $table->boolean('costing_by_marketing')->default(false);
            $table->boolean('boq')->default(false);
            
            $table->string('penalty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phcs');
    }
};
