<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('category_id');
            $table->string('eventTitle');
            $table->integer('eventPoints');
            $table->dateTime('eventStart');
            $table->dateTime('eventEnd');
            $table->text('eventSummary');
            $table->text('eventDescription');
            $table->string('eventImage');
            $table->text('eventImpact');
            $table->string('venueName');
            $table->string('zipCode');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->integer('eventMaximum');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('eventCategory_id')->on('event_categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

