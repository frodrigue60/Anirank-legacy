<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ArtistSongCountersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = \App\Models\User::withoutEvents(function () {
             return \App\Models\User::factory()->create();
        });
        $this->actingAs($user);
    }

    /** @test */
    public function it_updates_counters_when_attaching_songs()
    {
        $yearId = DB::table('years')->insertGetId(['name' => 2024, 'created_at' => now(), 'updated_at' => now()]);
        $seasonId = DB::table('seasons')->insertGetId(['name' => 'Winter', 'created_at' => now(), 'updated_at' => now()]);
        $animeId = DB::table('animes')->insertGetId(['title' => 'Test Anime', 'slug' => 'test-anime', 'year_id' => $yearId, 'season_id' => $seasonId, 'status' => true, 'created_at' => now(), 'updated_at' => now()]);

        $artist = Artist::withoutEvents(function () {
             return Artist::create(['name' => 'Test Artist', 'slug' => 'test-artist']);
        });
        
        $this->assertInstanceOf(Artist::class, $artist);

        $enabledSong = Song::withoutEvents(function () use ($animeId, $yearId, $seasonId) {
            return Song::create([
                'song_romaji' => 'Enabled Song', 
                'slug' => 'enabled-song', 
                'status' => true, 
                'anime_id' => $animeId,
                'year_id' => $yearId, 
                'season_id' => $seasonId
            ]);
        });
        
        $this->assertInstanceOf(Song::class, $enabledSong);

        $artist->songs()->attach($enabledSong->id);
        
        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);
        $this->assertEquals(0, $artist->disabled_songs);

        $disabledSong = Song::withoutEvents(function () use ($animeId, $yearId, $seasonId) {
            return Song::create([
                'song_romaji' => 'Disabled Song', 
                'slug' => 'disabled-song', 
                'status' => false, 
                'anime_id' => $animeId,
                'year_id' => $yearId, 
                'season_id' => $seasonId
            ]);
        });
        
        $artist->songs()->attach($disabledSong->id);
        
        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);
        $this->assertEquals(1, $artist->disabled_songs);
    }

    /** @test */
    public function it_updates_counters_when_detaching_songs()
    {
        $yearId = DB::table('years')->insertGetId(['name' => 2024, 'created_at' => now(), 'updated_at' => now()]);
        $seasonId = DB::table('seasons')->insertGetId(['name' => 'Winter', 'created_at' => now(), 'updated_at' => now()]);
        $animeId = DB::table('animes')->insertGetId(['title' => 'Test Anime', 'slug' => 'test-anime', 'year_id' => $yearId, 'season_id' => $seasonId, 'status' => true, 'created_at' => now(), 'updated_at' => now()]);

        $artist = Artist::withoutEvents(function () {
             return Artist::create(['name' => 'Test Artist', 'slug' => 'test-artist']);
        });

        $enabledSong = Song::withoutEvents(function () use ($animeId, $yearId, $seasonId) {
            return Song::create([
                'song_romaji' => 'Enabled Song', 
                'slug' => 'enabled-song', 
                'status' => true, 
                'anime_id' => $animeId,
                'year_id' => $yearId, 
                'season_id' => $seasonId
            ]);
        });
        
        $artist->songs()->attach($enabledSong->id);
        
        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);

        $artist->songs()->detach($enabledSong->id);
        $artist->refresh();
        $this->assertEquals(0, $artist->enabled_songs);
    }

    /** @test */
    public function it_updates_counters_when_song_status_changes()
    {
        $yearId = DB::table('years')->insertGetId(['name' => 2024, 'created_at' => now(), 'updated_at' => now()]);
        $seasonId = DB::table('seasons')->insertGetId(['name' => 'Winter', 'created_at' => now(), 'updated_at' => now()]);
        $animeId = DB::table('animes')->insertGetId(['title' => 'Test Anime', 'slug' => 'test-anime', 'year_id' => $yearId, 'season_id' => $seasonId, 'status' => true, 'created_at' => now(), 'updated_at' => now()]);

        $artist = Artist::withoutEvents(function () {
             return Artist::create(['name' => 'Test Artist', 'slug' => 'test-artist']);
        });

        $song = Song::withoutEvents(function () use ($animeId, $yearId, $seasonId) {
            return Song::create([
                'song_romaji' => 'Test Song', 
                'slug' => 'test-song', 
                'status' => true, 
                'anime_id' => $animeId,
                'year_id' => $yearId, 
                'season_id' => $seasonId
            ]);
        });

        $artist->songs()->attach($song->id);

        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);
        $this->assertEquals(0, $artist->disabled_songs);

        $song->update(['status' => false]);
        $artist->refresh();
        $this->assertEquals(0, $artist->enabled_songs);
        $this->assertEquals(1, $artist->disabled_songs);

        $song->update(['status' => true]);
        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);
        $this->assertEquals(0, $artist->disabled_songs);
    }

    /** @test */
    public function it_updates_counters_when_song_is_deleted()
    {
        $yearId = DB::table('years')->insertGetId(['name' => 2024, 'created_at' => now(), 'updated_at' => now()]);
        $seasonId = DB::table('seasons')->insertGetId(['name' => 'Winter', 'created_at' => now(), 'updated_at' => now()]);
        $animeId = DB::table('animes')->insertGetId(['title' => 'Test Anime', 'slug' => 'test-anime', 'year_id' => $yearId, 'season_id' => $seasonId, 'status' => true, 'created_at' => now(), 'updated_at' => now()]);

        $artist = Artist::withoutEvents(function () {
             return Artist::create(['name' => 'Test Artist', 'slug' => 'test-artist']);
        });

        $song = Song::withoutEvents(function () use ($animeId, $yearId, $seasonId) {
            return Song::create([
                'song_romaji' => 'Test Song', 
                'slug' => 'test-song', 
                'status' => true, 
                'anime_id' => $animeId,
                'year_id' => $yearId, 
                'season_id' => $seasonId
            ]);
        });
        
        $artist->songs()->attach($song->id);

        $artist->refresh();
        $this->assertEquals(1, $artist->enabled_songs);

        // Deleting a song should trigger a cascade or we rely on the pivot trigger if it is AFTER DELETE on artist_song?
        // Wait, Song::deleting hook calls $artist->songs()->detach() or similar?
        // Actually, Songs table has:
        // $table->foreignId('post_id')->references('id')->on('posts')->onDelete('cascade');
        // And artist_song pivot usually has cascade.
        // Let's check artist_song migration for onDelete cascade.
        $song->delete();
        $artist->refresh();
        $this->assertEquals(0, $artist->enabled_songs);
    }
}
