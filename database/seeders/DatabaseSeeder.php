<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed initial lookup tables and roles
        $this->call([
            RoleSeeder::class,
            PermissionRoleSeeder::class,
            SeasonSeeder::class,
            YearSeeder::class,
            XpSystemSeeder::class,
        ]);

        // Create admin user or update if exists
        // Note: withoutEvents() suppresses the HasUuid trait's creating event,
        // so we set uuid explicitly on first creation only.
        $user = User::withoutEvents(function () {
            $existing = User::where('email', 'frodrigue60@gmail.com')->first();

            if ($existing) {
                $existing->update([
                    'name' => 'Luis Rodz',
                    'slug' => Str::slug('Luis Rodz'),
                    'password' => bcrypt('a12edc21cd'),
                ]);

                return $existing;
            }

            return User::create([
                'uuid' => (string) Str::uuid7(),
                'email' => 'frodrigue60@gmail.com',
                'name' => 'Luis Rodz',
                'slug' => Str::slug('Luis Rodz'),
                'password' => bcrypt('a12edc21cd'),
            ]);
        });

        // Assign owner role (idempotent)
        $ownerRole = DB::table('roles')->where('slug', 'owner')->first();
        if ($ownerRole) {
            $hasRole = DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $ownerRole->id)
                ->exists();

            if (! $hasRole) {
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
