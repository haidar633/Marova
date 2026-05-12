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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Add this line
            $table->string('speciality')->nullable();
            $table->string('dr_percentage')->nullable();  //for doctor
            $table->string('dr_is_active')->default('1');  // default to active
            $table->string('center_percentage')->nullable();  //for center            $table->timestamps();
            $table->string('color')->nullable()->default('#000');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
