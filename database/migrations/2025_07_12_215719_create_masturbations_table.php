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
        Schema::create('masturbations', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->time('entry_time')->nullable();
            $table->string('reason')->nullable();
            $table->decimal('rating', 2, 1)->nullable(); // e.g., 4.5
            $table->text('description')->nullable();
            $table->boolean('vaseline_used')->default(false);
            $table->unsignedInteger('count')->default(1);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masturbations');
    }
};
