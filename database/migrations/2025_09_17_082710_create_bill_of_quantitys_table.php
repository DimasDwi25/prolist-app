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
        Schema::create('bill_of_quantitys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('pn_number')->on('projects')->onDelete('cascade');
            $table->integer('item_number');
            $table->text('description');
            $table->decimal('material_value', 18, 2)->default(0);
            $table->decimal('engineer_value', 18, 2)->default(0);
            $table->decimal('material_portion', 8, 4)->default(0); // persen max 100.0000
            $table->decimal('engineer_portion', 8, 4)->default(0);

            $table->decimal('progress_material', 5, 2)->default(0); // %
            $table->decimal('progress_engineer', 5, 2)->default(0); // %
            $table->decimal('total_progress', 5, 2)->default(0);    // %
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_quantitys');
    }
};
