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
                ->nullable()
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

            $table->dateTime('handover_date');

            $table->boolean('costing_by_marketing')->default(false);
            $table->boolean('boq')->default(false);
            $table->string('retention')->nullable();
            $table->string('warranty')->nullable();
            $table->string('penalty')->nullable();
            $table->boolean('scope_of_work_approval')->default(false);
            $table->boolean('organization_chart')->default(false);
            $table->boolean('project_schedule')->default(false);
            $table->boolean('component_list')->default(false);
            $table->boolean('progress_claim_report')->default(false);
            $table->boolean('component_approval_list')->default(false);
            $table->boolean('design_approval_draw')->default(false);
            $table->boolean('shop_draw')->default(false);
            $table->boolean('fat_sat_forms')->default(false);
            $table->boolean('daily_weekly_progress_report')->default(false);
            $table->boolean('do_packing_list')->default(false);
            $table->boolean('site_testing_commissioning_report')->default(false);
            $table->boolean('as_build_draw')->default(false);
            $table->boolean('manual_documentation')->default(false);
            $table->boolean('accomplishment_report')->default(false);
            $table->boolean('client_document_requirements')->default(false);
            $table->boolean('job_safety_analysis')->default(false);
            $table->boolean('risk_assessment')->default(false);
            $table->boolean('tool_list')->default(false);
            $table->dateTime('handover_dates')->nullable();
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
