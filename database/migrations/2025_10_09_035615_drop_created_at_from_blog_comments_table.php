<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            if (Schema::hasColumn('blog_comments', 'createdAt')) {
                $table->dropColumn('createdAt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->timestamp('createdAt')->useCurrent();
        });
    }
};
