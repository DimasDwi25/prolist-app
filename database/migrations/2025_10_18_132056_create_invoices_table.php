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
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('invoice_id')->primary();
            $table->integer('invoice_number_in_project');
            $table->foreignId('project_id')->constrained('projects', 'pn_number')->onDelete('cascade');
            $table->foreignId('invoice_type_id')->constrained('invoice_types', 'id')->onDelete('cascade');
            $table->string('no_faktur', 1250)->nullable();
            $table->date('invoice_date');
            $table->text('invoice_description')->nullable();
            $table->decimal('invoice_value', 18, 2);
            $table->date('invoice_due_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
