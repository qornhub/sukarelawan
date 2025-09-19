<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registration_skill', function (Blueprint $table) {
            $table->uuid('registration_id');
            $table->uuid('skill_id');

            $table->primary(['registration_id', 'skill_id']);

            $table->foreign('registration_id')->references('registration_id')->on('event_registrations')->onDelete('cascade');
            $table->foreign('skill_id')->references('skill_id')->on('skills')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_skill');
    }
};
