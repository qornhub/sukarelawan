<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // 🔴 IMPORTANT: match this type to your original migration
            // If you used `uuid('category_id')` originally:
            $table->uuid('category_id')->nullable()->change();

            // If instead you used `string('category_id', 36)` originally,
            // then use:
            // $table->string('category_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // revert back to NOT NULL (if you ever roll back)
            $table->uuid('category_id')->nullable(false)->change();

            // or string version:
            // $table->string('category_id', 36)->nullable(false)->change();
        });
    }
};
