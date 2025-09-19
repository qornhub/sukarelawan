<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Support\Str;

class UserPointSeeder extends Seeder
{
    public function run(): void
    {
        // Example point distribution
        $points = [1, 2, 10, 20, 0, 40, 100, 200, 300, 400];

        $volunteers = User::whereHas('role', function ($q) {
            $q->where('roleName', 'volunteer');
        })->take(10)->get();

        foreach ($volunteers as $index => $volunteer) {
            UserPoint::create([
                'userPoint_id' => (string) Str::uuid(),
                'user_id' => $volunteer->id,
                'points' => $points[$index] ?? 0,
                'activityType' => 'Admin Adjustment',
            ]);
        }
    }
}
