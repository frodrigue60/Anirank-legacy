<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create admin user or update if exists
        $user = User::updateOrCreate(
            ['email' => 'frodrigue60@gmail.com'],
            [
                'name' => 'Luis Rodz',
                'slug' => Str::slug('Luis Rodz'),
                'password' => bcrypt('a12edc21cd'),
            ]
        );

        // Assign admin role (idempotent)
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        if ($adminRole) {
            $hasRole = DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $adminRole->id)
                ->exists();

            if (!$hasRole) {
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $adminRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
