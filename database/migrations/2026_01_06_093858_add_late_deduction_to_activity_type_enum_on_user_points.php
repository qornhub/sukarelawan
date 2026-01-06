<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        DB::statement("
            ALTER TABLE user_points
            MODIFY activityType ENUM(
                'Event Attendance',
                'Bonus',
                'Referral',
                'Admin Adjustment',
                'Badge Claimed',
                'Late Deduction'
            ) NOT NULL DEFAULT 'Event Attendance'
        ");
    }

    public function down(): void
    {
        
        DB::statement("
            UPDATE user_points
            SET activityType = 'Event Attendance'
            WHERE activityType = 'Late Deduction'
        ");

        DB::statement("
            ALTER TABLE user_points
            MODIFY activityType ENUM(
                'Event Attendance',
                'Bonus',
                'Referral',
                'Admin Adjustment',
                'Badge Claimed'
            ) NOT NULL DEFAULT 'Event Attendance'
        ");
    }
};
