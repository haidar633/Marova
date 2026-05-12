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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attempt_id')
                ->nullable()
                ->constrained('attempts')
                ->onDelete('cascade');


            $table->date('scheduled_date')->index(); // for calendar views
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->enum('status', ['scheduled', 'done', 'cancelled', 'missed'])
                ->default('scheduled');

            $table->text('sales_note')->nullable();
            $table->text('staff_note')->nullable();
//            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
