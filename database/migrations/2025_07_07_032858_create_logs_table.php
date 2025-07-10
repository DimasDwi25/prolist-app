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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_log_id')->constrained('categorie_logs')->onDelete('cascade');
            $table->foreignId('users_id')->constrained('users'); // created by

            $table->text('logs');
            $table->dateTime('tgl_logs');

            $table->enum('status', ['open', 'close']);
            $table->dateTime('closing_date')->nullable();
            $table->foreignId('closing_users')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('response_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('need_response')->default(false);

            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
