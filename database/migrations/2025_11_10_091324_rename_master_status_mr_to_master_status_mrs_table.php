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
        Schema::rename('master_status_mr', 'master_status_mrs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('master_status_mrs', 'master_status_mr');
    }
};
