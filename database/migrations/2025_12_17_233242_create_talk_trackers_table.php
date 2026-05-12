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
        Schema::create('talk_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('conversation_date');
            $table->time('conversation_time')->nullable();
            $table->string('topic');
            $table->decimal('connection_rating', 2, 1)->nullable()->comment('1-5 star rating for how heard I felt');
            $table->text('resolution_summary')->nullable();
            $table->foreignId('mood_journal_id')->nullable()->constrained('mood_journals')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talk_trackers');
    }
};
