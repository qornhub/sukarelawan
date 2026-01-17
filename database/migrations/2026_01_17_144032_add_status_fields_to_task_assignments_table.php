<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('assignedDate');
            $table->text('reject_reason')->nullable()->after('status');
            $table->timestamp('responded_at')->nullable()->after('reject_reason');
        });
    }

    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropColumn(['status', 'reject_reason', 'responded_at']);
        });
    }
};
