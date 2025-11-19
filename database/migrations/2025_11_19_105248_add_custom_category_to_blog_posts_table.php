<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('blog_posts', function (Blueprint $table) {
        $table->string('custom_category', 255)->nullable()->after('category_id');
    });
}

public function down()
{
    Schema::table('blog_posts', function (Blueprint $table) {
        $table->dropColumn('custom_category');
    });
}

};
