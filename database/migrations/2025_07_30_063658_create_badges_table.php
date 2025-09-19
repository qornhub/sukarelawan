<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('badge_id')->primary();
            $table->uuid('badgeCategory_id');
            $table->string('badgeName');
            $table->text('badgeDescription')->nullable();
            $table->integer('pointsRequired');
            $table->string('badgeImage')->nullable();
            $table->timestamps();

            $table->foreign('badgeCategory_id')->references('badgeCategory_id')->on('badge_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
