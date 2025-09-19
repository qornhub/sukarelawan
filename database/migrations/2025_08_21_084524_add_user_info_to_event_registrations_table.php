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
        $table->string('name')->nullable();
        $table->string('email')->nullable();
        $table->string('contactNumber')->nullable();
        $table->integer('age')->nullable();
        $table->string('gender')->nullable();
        $table->string('address')->nullable();
    });
}

public function down()
{
    Schema::table('event_registrations', function (Blueprint $table) {
        $table->dropColumn(['name', 'email', 'contactNumber', 'age', 'gender', 'address']);
    });
}

};
