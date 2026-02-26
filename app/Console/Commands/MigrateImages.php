<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateImages extends Command
{
    protected $signature = 'migrate:images';
    protected $description = 'Migrate existing image paths to the polymorphic images table';

    public function handle()
    {
        $this->info('Starting image migration...');

        // 1. Migrate Animes (Thumbnails & Banners)
        \App\Models\Anime::all()->each(function ($anime) {
            if ($anime->thumbnail) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $anime->id,
                    'imageable_type' => \App\Models\Anime::class,
                    'type' => 'thumbnail',
                    'path' => $anime->thumbnail,
                    'disk' => 'public'
                ]);
            }
            if ($anime->banner) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $anime->id,
                    'imageable_type' => \App\Models\Anime::class,
                    'type' => 'banner',
                    'path' => $anime->banner,
                    'disk' => 'public'
                ]);
            }
        });

        // 2. Migrate Artists (Thumbnails)
        \App\Models\Artist::all()->each(function ($artist) {
            if ($artist->thumbnail) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $artist->id,
                    'imageable_type' => \App\Models\Artist::class,
                    'type' => 'thumbnail',
                    'path' => $artist->thumbnail,
                    'disk' => 'public'
                ]);
            }
        });

        // 3. Migrate Users (Avatar & Banner)
        \App\Models\User::all()->each(function ($user) {
            if ($user->image) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $user->id,
                    'imageable_type' => \App\Models\User::class,
                    'type' => 'avatar',
                    'path' => $user->image,
                    'disk' => 'public'
                ]);
            }
            if ($user->banner) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $user->id,
                    'imageable_type' => \App\Models\User::class,
                    'type' => 'banner',
                    'path' => $user->banner,
                    'disk' => 'public'
                ]);
            }
        });

        $this->info('Image migration completed successfully!');
    }
}
