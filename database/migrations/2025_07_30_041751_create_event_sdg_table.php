<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_sdg', function (Blueprint $table) {
            $table->uuid('event_id');
            $table->uuid('sdg_id');

            $table->primary(['event_id', 'sdg_id']);

            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('sdg_id')->references('sdg_id')->on('sdgs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sdg');
    }
};

