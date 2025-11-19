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
    DB::statement("
        ALTER TABLE user_points 
        MODIFY activityType 
        ENUM('Event Attendance', 'Bonus', 'Referral', 'Admin Adjustment', 'Badge Claimed')
        NOT NULL DEFAULT 'Event Attendance'
    ");
}

public function down()
{
    DB::statement("
        ALTER TABLE user_points 
        MODIFY activityType 
        ENUM('Event Attendance', 'Bonus', 'Referral', 'Admin Adjustment')
        NOT NULL DEFAULT 'Event Attendance'
    ");
}

};
