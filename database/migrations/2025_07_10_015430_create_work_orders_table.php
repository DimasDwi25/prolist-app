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
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->dateTime('wo_date');
            $table->integer('wo_number_in_project');
            $table->string('wo_kode_no');
            $table->foreignId('pic1')->nullable()->constrained('users');
            $table->foreignId('pic2')->nullable()->constrained('users');
            $table->foreignId('pic3')->nullable()->constrained('users');
            $table->foreignId('pic4')->nullable()->constrained('users');
            $table->foreignId('pic5')->nullable()->constrained('users');

            $table->foreignId('role_pic_1')->nullable()->constrained('roles');
            $table->foreignId('role_pic_2')->nullable()->constrained('roles');
            $table->foreignId('role_pic_3')->nullable()->constrained('roles');
            $table->foreignId('role_pic_4')->nullable()->constrained('roles');
            $table->foreignId('role_pic_5')->nullable()->constrained('roles');

            $table->integer('total_mandays_eng');
            $table->integer('total_mandays_elect');

            $table->boolean('add_work')->default(false);

            $table->text('work_description')->nullable();

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
