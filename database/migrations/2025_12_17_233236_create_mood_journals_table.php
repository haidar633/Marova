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
        Schema::create('mood_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('entry_date');
            $table->time('entry_time')->nullable();
            $table->enum('emotional_state', ['Secure', 'Anxious/Toxic', 'Overwhelmed', 'Happy', 'Sad', 'Angry', 'Calm', 'Excited', 'Stressed', 'Other'])->default('Secure');
            $table->text('pov')->nullable()->comment('Point of view - internal thoughts');
            $table->boolean('discussed_with_partner')->default(false);
            $table->unsignedBigInteger('vibe_check_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_journals');
    }
};
