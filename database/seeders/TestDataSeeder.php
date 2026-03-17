<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure auxiliary data exists
        $yearId = DB::table('years')->where('name', 2025)->value('id');
        if (!$yearId) {
            $yearId = DB::table('years')->insertGetId([
                'name' => 2025,
                'current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $seasonId = DB::table('seasons')->where('name', 'SUMMER')->value('id');
        if (!$seasonId) {
            $seasonId = DB::table('seasons')->insertGetId([
                'name' => 'SUMMER',
                'current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $formatId = DB::table('formats')->where('slug', 'tv')->value('id');
        if (!$formatId) {
            $formatId = DB::table('formats')->insertGetId([
                'name' => 'TV',
                'slug' => 'tv',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $animeData = [
            ['title' => 'Oshi no Ko', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx150672-979998818820.jpg'],
            ['title' => 'Sousou no Frieren', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx154587-87.jpg'],
            ['title' => 'Jujutsu Kaisen', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx113415-8121111161.jpg'],
            ['title' => 'Chainsaw Man', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx127230-8111111161.jpg'],
            ['title' => 'Spy x Family', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx140960-vN39AmOWrVB5.jpg'],
            ['title' => 'Lycoris Recoil', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx143270-iZOJX2DMUFMC.jpg'],
            ['title' => 'Cyberpunk Edgerunners', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx120377-8111111161.jpg'],
            ['title' => 'Blue Lock', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx137822-8111111161.jpg'],
            ['title' => 'Vinland Saga', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx101348-NhSwxv7HY9y9.jpg'],
            ['title' => 'Mushoku Tensei', 'cover' => 'https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx108465-RVIY9TGd737H.jpg']
        ];

        foreach ($animeData as $data) {
            $animeSlug = Str::slug($data['title']);
            
            // Create Anime
            $animeId = DB::table('animes')->where('slug', $animeSlug)->value('id');
            if (!$animeId) {
                $animeId = DB::table('animes')->insertGetId([
                    'title' => $data['title'],
                    'slug' => $animeSlug,
                    'description' => "Test description for " . $data['title'],
                    'status' => 1,
                    'year_id' => $yearId,
                    'season_id' => $seasonId,
                    'format_id' => $formatId,
                    'cover' => $data['cover'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create Artist
            $artistName = "Artist of " . $data['title'];
            $artistSlug = Str::slug($artistName);
            $artistId = DB::table('artists')->where('slug', $artistSlug)->value('id');
            if (!$artistId) {
                $artistId = DB::table('artists')->insertGetId([
                    'name' => $artistName,
                    'slug' => $artistSlug,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create Song
            $songTitle = "The Theme of " . $data['title'];
            $songSlug = Str::slug($songTitle);
            $songId = DB::table('songs')->where('slug', $songSlug)->value('id');
            if (!$songId) {
                $songId = DB::table('songs')->insertGetId([
                    'song_romaji' => $songTitle,
                    'slug' => $songSlug,
                    'type' => 'OP',
                    'theme_num' => '1',
                    'status' => 1,
                    'anime_id' => $animeId,
                    'year_id' => $yearId,
                    'season_id' => $seasonId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Attach Artist to Song (if not already attached)
            $existingAttachment = DB::table('artist_song')
                ->where('artist_id', $artistId)
                ->where('song_id', $songId)
                ->exists();
            
            if (!$existingAttachment) {
                DB::table('artist_song')->insert([
                    'artist_id' => $artistId,
                    'song_id' => $songId,
                ]);
            }

            // Create Song Variant
            $variantSlug = $songSlug . '-v1';
            $variantId = DB::table('song_variants')->where('slug', $variantSlug)->value('id');
            if (!$variantId) {
                $variantId = DB::table('song_variants')->insertGetId([
                    'version_number' => 1,
                    'song_id' => $songId,
                    'slug' => $variantSlug,
                    'status' => 1,
                    'year_id' => $yearId,
                    'season_id' => $seasonId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create Video (if not already exists for this variant)
            $existingVideo = DB::table('videos')->where('song_variant_id', $variantId)->exists();
            if (!$existingVideo) {
                DB::table('videos')->insert([
                    'embed_code' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'song_variant_id' => $variantId,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
