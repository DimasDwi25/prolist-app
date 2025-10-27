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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // siapa yang melakukan aksi
            $table->string('action', 50); // jenis aksi: create, update, delete, login, logout, approve, dsb
            $table->string('model_type', 255)->nullable(); // nama model, misal: "App\\Models\\Project"
            $table->string('model_id')->nullable(); // ID data yang diubah
            $table->text('description')->nullable(); // deskripsi singkat aksi
            $table->json('changes')->nullable(); // simpan data perubahan sebelum/sesudah
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
