<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    DB::statement('ALTER TABLE blog_posts MODIFY category_id CHAR(36) NULL;');
}

public function down()
{
    DB::statement('ALTER TABLE blog_posts MODIFY category_id CHAR(36) NOT NULL;');
}
};
