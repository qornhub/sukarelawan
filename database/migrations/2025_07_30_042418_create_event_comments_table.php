<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_comments', function (Blueprint $table) {
            $table->uuid('eventComment_id')->primary();
            $table->uuid('event_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_comments');
    }
};

