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
        Schema::table('phcs', function (Blueprint $table) {
            //
            $table->boolean('retention')->nullable()->default(false);
            $table->boolean('warranty')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phcs', function (Blueprint $table) {
            //
        });
    }
};
