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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation
            $table->string('approvable_type'); // 'PHC', 'Log', 'WorkOrder', dsb.
            $table->unsignedBigInteger('approvable_id');

            $table->foreignId('user_id')->constrained('users')->noActionOnDelete(); // Approver
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('validated_at')->nullable();
            $table->string('pin_hash')->nullable(); // Jika pakai PIN untuk approve
            $table->text('remarks')->nullable(); // Catatan approval

            // Index untuk query lebih cepat
            $table->index(['approvable_type', 'approvable_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
