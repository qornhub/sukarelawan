<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        // Get volunteer role id (assuming your roles table already has a "volunteer" role)
        $volunteerRole = Role::where('roleName', 'volunteer')->first();

        // Create 10 volunteers
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Volunteer $i",
                'email' => "volunteer$i@example.com",
                'password' => Hash::make('password'), // default password
                'role_id' => $volunteerRole->role_id,
            ]);
        }
    }
}
