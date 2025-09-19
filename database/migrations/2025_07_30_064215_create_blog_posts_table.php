<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('blogPost_id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('category_id');
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->dateTime('publishedDate');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('blogCategory_id')->on('blog_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
