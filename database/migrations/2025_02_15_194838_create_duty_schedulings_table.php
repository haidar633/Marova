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
        Schema::create('duty_schedulings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('scheduled_date');
            $table->longText('description')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duty_schedulings');
    }
};
