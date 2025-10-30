<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('phcs', function (Blueprint $table) {
            $table->decimal('retention_percentage', 5, 2)->nullable();
            $table->integer('retention_months')->nullable();
            $table->date('warranty_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phcs', function (Blueprint $table) {
            $table->dropColumn(['retention', 'retention_percentage', 'retention_months', 'warranty', 'warranty_date']);
        });
    }
};
