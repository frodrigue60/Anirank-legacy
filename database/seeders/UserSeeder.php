<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'frodrigue60@gmail.com';
        $adminUuid = '018e6c43-1e5e-7a9b-8c6d-5f4e2d1c0b9a';

        // 1. Create or Update admin user (idempotent)
        DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'uuid' => $adminUuid,
                'name' => 'Luis Rodz',
                'slug' => 'luis-rodz',
                'password' => '$2a$10$XxVaW4uBeckodmw2883vv.PXDUw8HQjYFltqCYHCbHx8oR/Ksj3l6', // a12edc21cd
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Get IDs for role assignment
        $user = DB::table('users')->where('email', $email)->first();
        $ownerRole = DB::table('roles')->where('slug', 'owner')->first();

        // 2. Assign owner role if both user and role exist
        if ($user && $ownerRole) {
            $hasRole = DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $ownerRole->id)
                ->exists();

            if (!$hasRole) {
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $ownerRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
