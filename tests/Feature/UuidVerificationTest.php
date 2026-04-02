<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Anime;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class UuidVerificationTest extends TestCase
{
    // We don't use RefreshDatabase here to check existing records too, 
    // or we can just use a separate test for new records.
    
    /** @test */
    public function new_records_generate_uuid_v7()
    {
        // We use withoutEvents if we don't want other hooks, but here we WANT the HasUuid hook.
        $user = User::create([
            'name' => 'Test User ' . Uniqid(),
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertNotNull($user->uuid);
        $this->assertTrue(Str::isUuid($user->uuid));
        
        // Basic check for UUID v7 (starts with timestamp bits)
        // UUID v7 has a '7' at the 13th character (index 12 or 14 including dashes)
        $this->assertEquals('7', $user->uuid[14]);
    }

    /** @test */
    public function existing_records_have_uuid()
    {
        $user = User::first();
        if ($user) {
            $this->assertNotNull($user->uuid);
            $this->assertTrue(Str::isUuid($user->uuid));
            $this->assertEquals('7', $user->uuid[14]);
        }
        
        $anime = Anime::first();
        if ($anime) {
            $this->assertNotNull($anime->uuid);
            $this->assertEquals('7', $anime->uuid[14]);
        }
    }
}
