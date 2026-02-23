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

        // Create admin user
        $user = new User;
        $user->name = 'Luis Rodz';
        $user->slug = Str::slug($user->name);
        $user->email = 'frodrigue60@gmail.com';
        $user->password = bcrypt('a12edc21cd');
        $user->save();

        // Assign admin role
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        if ($adminRole) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
