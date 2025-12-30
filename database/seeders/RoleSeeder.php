<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           foreach (['volunteer', 'ngo', 'admin'] as $role) {
    Role::firstOrCreate(
        ['roleName' => $role],
        ['role_id' => Str::uuid()]
    );
}
    }
}
