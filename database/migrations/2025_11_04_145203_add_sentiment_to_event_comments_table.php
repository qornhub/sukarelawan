<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentimentToEventCommentsTable extends Migration
{
    public function up()
    {
        Schema::table('event_comments', function (Blueprint $table) {
            $table->string('sentiment', 20)->nullable()->after('content'); // Positive | Negative | Toxic
            $table->decimal('sentiment_confidence', 5, 4)->nullable()->after('sentiment'); // 0.0000 - 1.0000
        });
    }

    public function down()
    {
        Schema::table('event_comments', function (Blueprint $table) {
            $table->dropColumn(['sentiment_confidence', 'sentiment']);
        });
    }
}
