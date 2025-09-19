<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            // Rename pointTotal → points
            $table->renameColumn('pointTotal', 'points');

            // Add activityType (with default)
            $table->enum('activityType', [
                'Event Attendance',
                'Bonus',
                'Referral',
                'Admin Adjustment',
            ])->default('Event Attendance')->after('points');

            // Optional foreign keys if needed later
            $table->char('event_id', 36)->nullable()->after('activityType');
            $table->char('attendance_id', 36)->nullable()->after('event_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            // Rollback changes
            $table->renameColumn('points', 'pointTotal');
            $table->dropColumn(['activityType', 'event_id', 'attendance_id']);
        });
    }
};

