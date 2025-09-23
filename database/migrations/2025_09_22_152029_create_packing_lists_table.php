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
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->string('pl_id')->primary();
            $table->string('pl_number');
            $table->foreignId('pn_id')
                ->nullable()
                ->constrained('projects', 'pn_number')
                ->noActionOnDelete();
            $table->string('destination');
            $table->text('expedition_name');
            $table->dateTime('pl_date')->nullable();
            $table->dateTime('ship_date')->nullable();
            $table->enum('pl_type',['internal', 'client', 'expedition']);
            $table->foreignId('int_pic')->nullable()->constrained('users', 'id');
            $table->string('client_pic')->nullable();
            $table->dateTime('receive_date')->nullable();
            $table->dateTime('pl_return_date')->nullable();
            $table->text('remark');
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_lists');
    }
};
