<?php

// database/migrations/2025_xx_xx_add_sentiment_to_blog_comments.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentimentToBlogComments extends Migration
{
    public function up()
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->string('sentiment', 20)->nullable()->after('content'); // Positive, Negative, Toxic
            $table->decimal('sentiment_confidence', 5, 4)->nullable()->after('sentiment'); // 0.0000 - 1.0000
        });
    }

    public function down()
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropColumn(['sentiment_confidence', 'sentiment']);
        });
    }
}
