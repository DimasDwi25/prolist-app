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
        Schema::table('phc_approvals', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('ho_engineering_id')->nullable()->after('user_id');
            $table->string('ho_engineering')->nullable()->after('ho_engineering_id');

            $table->foreign('ho_engineering_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phc_approvals', function (Blueprint $table) {
            //
            $table->dropForeign(['ho_engineering_id']);
            $table->dropColumn(['ho_engineering_id', 'ho_engineering']);
        });
    }
};
