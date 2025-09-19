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
    Schema::table('event_registrations', function (Blueprint $table) {
        $table->string('skill')->nullable()->after('volunteeringExperience');
    });
}

public function down()
{
    Schema::table('event_registrations', function (Blueprint $table) {
        $table->dropColumn('skill');
    });
}

};
