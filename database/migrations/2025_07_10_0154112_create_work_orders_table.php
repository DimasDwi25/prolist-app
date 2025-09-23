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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'pn_number')->onDelete('cascade');
            $table->dateTime('wo_date');
            $table->integer('wo_number_in_project');
            $table->string('wo_kode_no');

            // total mandays
            $table->integer('total_mandays_eng')->default(0);
            $table->integer('total_mandays_elect')->default(0);

            // tambahan pekerjaan
            $table->boolean('add_work')->default(false);

            $table->foreignId('approved_by')->nullable()->constrained('users', 'id');
            $table->enum('status', ['waiting approval', 'approved', 'finished']);

            $table->dateTime('start_working_date')->nullable();
            $table->dateTime('end_working_date')->nullable();

            $table->integer('wo_count')->nullable();

            $table->boolean('client_approved')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users', 'id');

            $table->foreignId('accepted_by')->nullable()->constrained('users', 'id');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
