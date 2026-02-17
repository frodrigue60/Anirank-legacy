<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
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

        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $role = DB::table('roles')->where('slug', $user->type)->first();
            if ($role) {
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_user')->truncate();
        DB::table('roles')->truncate();
    }
};
