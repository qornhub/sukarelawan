<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->uuid('blogComment_id')->primary();
            $table->uuid('blogPost_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamps();

            $table->foreign('blogPost_id')->references('blogPost_id')->on('blog_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
