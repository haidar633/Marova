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
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->boolean('successful')->default(false);
            $table->decimal('satisfaction_rating', 3, 1)->nullable();
            $table->integer('attempt_nb')->default(1);
            $table->text('description')->nullable();
            $table->text('attempt_reason')->nullable();
            $table->time('attempt_time')->nullable();
            $table->date('attempt_date');
            $table->integer('duration_minutes')->nullable();
            $table->boolean('lubrication_used')->nullable()->default(false);
            $table->string('attempt_type')->nullable();
            $table->text('positions')->nullable();
            $table->decimal('her_rating', 3, 1)->nullable();
            $table->text('her_description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
