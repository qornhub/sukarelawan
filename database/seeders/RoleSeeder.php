<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           DB::table('roles')->insert([
        ['role_id' => Str::uuid(), 'roleName' => 'volunteer'],
        ['role_id' => Str::uuid(), 'roleName' => 'ngo'],
        ['role_id' => Str::uuid(), 'roleName' => 'admin'],
    ]);
    }
}
