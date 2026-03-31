<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table with default roles.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Owner', 'slug' => 'owner', 'description' => 'Owner of the website'],
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full system access'],
            ['name' => 'Editor', 'slug' => 'editor', 'description' => 'Content management'],
            ['name' => 'Creator', 'slug' => 'creator', 'description' => 'Original content creator'],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Standard user'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
