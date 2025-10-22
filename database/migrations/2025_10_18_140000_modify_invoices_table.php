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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_date', 'payment_amount']);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->date('payment_date')->nullable();
            $table->decimal('payment_amount', 18, 2)->nullable();
        });
    }
};
