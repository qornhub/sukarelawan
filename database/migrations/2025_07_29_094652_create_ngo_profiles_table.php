<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ngo_profiles', function (Blueprint $table) {
            $table->uuid('ngo_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('organizationName');
            $table->string('registrationNumber');
            $table->string('country');
            $table->string('contactNumber');
            $table->text('about')->nullable();
            $table->string('website')->nullable();
            $table->string('coverPhoto')->nullable();
            $table->string('profilePhoto')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ngo_profiles');
    }
};

