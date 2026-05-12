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
        Schema::table('mood_journals', function (Blueprint $table) {
            $table->foreign('vibe_check_id')->references('id')->on('vibe_checks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_journals', function (Blueprint $table) {
            $table->dropForeign(['vibe_check_id']);
        });
    }
};
