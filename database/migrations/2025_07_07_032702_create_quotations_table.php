<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('quotation_number')->nullable()->index();
            $table->dateTime('inquiry_date');
            //
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            $table->string('title_quotation');
            $table->dateTime('quotation_date');
            $table->string('no_quotation')->unique();
            $table->string('quotation_weeks');
            $table->decimal('quotation_value', 15, 2);
            $table->dateTime('po_date')->nullable();
            $table->string('sales_weeks')->nullable();
            $table->string('po_number')->nullable();
            $table->decimal('po_value')->nullable();
            $table->dateTime('revision_quotation_date')->nullable();
            $table->string('client_pic');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('revisi')->nullable();
            $table->enum('status', ['A', 'D', 'E', 'F', 'O'])->default('O');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
