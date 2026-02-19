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

        // 1. Migrate Posts (Thumbnails & Banners)
        \App\Models\Post::all()->each(function ($post) {
            if ($post->thumbnail) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $post->id,
                    'imageable_type' => \App\Models\Post::class,
                    'type' => 'thumbnail',
                    'path' => $post->thumbnail,
                    'disk' => 'public'
                ]);
            }
            if ($post->banner) {
                \App\Models\Image::firstOrCreate([
                    'imageable_id' => $post->id,
                    'imageable_type' => \App\Models\Post::class,
                    'type' => 'banner',
                    'path' => $post->banner,
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
