<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->uuid('volunteer_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('contactNumber')->nullable();
            $table->string('country')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('coverPhoto')->nullable();
            $table->string('profilePhoto')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_profiles');
    }
};

