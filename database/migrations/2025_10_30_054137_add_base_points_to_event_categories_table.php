<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('event_categories', function (Blueprint $table) {
        $table->integer('basePoints')->default(10)->after('eventCategoryName');
    });
}

public function down()
{
    Schema::table('event_categories', function (Blueprint $table) {
        $table->dropColumn('basePoints');
    });
}
};
