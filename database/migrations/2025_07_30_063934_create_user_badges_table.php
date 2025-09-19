<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->uuid('userBadge_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('badge_id');
            $table->dateTime('earnedDate');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('badge_id')->references('badge_id')->on('badges')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
