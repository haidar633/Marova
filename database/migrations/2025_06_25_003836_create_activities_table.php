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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('activity_type', ['masturbation', 'sex', 'porn_viewing']);
            $table->date('date');
            $table->integer('duration_minutes');
            $table->decimal('mood_before', 3, 1)->nullable();
            $table->decimal('mood_after', 3, 1)->nullable();
            $table->text('notes')->nullable();

            // ADD THIS LINE
            $table->json('positions')->nullable(); // To store an array of position IDs

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
