<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert existing string values to decimal
        DB::statement("UPDATE projects SET project_progress = TRY_CAST(project_progress AS DECIMAL(5,2)) WHERE project_progress IS NOT NULL");

        // Then alter the column type
        DB::statement("ALTER TABLE projects ALTER COLUMN project_progress DECIMAL(5,2) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to string
        DB::statement("ALTER TABLE projects ALTER COLUMN project_progress NVARCHAR(255) NULL");
    }
};
