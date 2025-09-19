<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->uuid('registration_id')->primary();
            $table->uuid('event_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('registrationDate');
            $table->string('status');
            $table->string('company')->nullable();
            $table->text('volunteeringExperience')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->string('emergencyContactNumber')->nullable();
            $table->string('emergencyContact')->nullable();
            $table->string('contactRelationship')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};

