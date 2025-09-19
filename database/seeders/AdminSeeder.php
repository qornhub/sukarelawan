<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AdminProfile;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get or create admin role
        $adminRole = Role::firstOrCreate([
            'roleName' => 'admin'
        ], [
            'role_id' => Str::uuid()
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('securepassword'),
                'role_id' => $adminRole->role_id,
            ]
        );

        // Create admin profile
        AdminProfile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'admin_id' => Str::uuid(),
                'name' => 'System Admin',
                 'profilePhoto' => 'images/default_admin.jpg' // <-- Path to your default photo
            ]
        );
    }
}


