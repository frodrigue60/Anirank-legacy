<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table with default roles.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Owner',         'slug' => 'owner',  'description' => 'Owner of the website',        'weight' => 100],
            ['name' => 'Administrator', 'slug' => 'admin',  'description' => 'Full system access',          'weight' => 30],
            ['name' => 'Editor',        'slug' => 'editor', 'description' => 'Content management',          'weight' => 20],
            ['name' => 'Creator',       'slug' => 'creator','description' => 'Original content creator',     'weight' => 10],
            ['name' => 'User',          'slug' => 'user',   'description' => 'Standard user',               'weight' => 0],
        ];

        Role::withoutEvents(function () use ($roles) {
            foreach ($roles as $role) {
                Role::updateOrCreate(
                    ['slug' => $role['slug']],
                    [
                        'name' => $role['name'],
                        'description' => $role['description'],
                        'weight' => $role['weight'],
                    ]
                );
            }
        });
    }
}
