<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Announcement::create([
            'title' => 'Welcome to Anirank!',
            'content' => 'Rate your favorite anime openings and endings now.',
            'type' => 'info',
            'icon' => 'campaign',
            'priority' => 10,
        ]);

        Announcement::create([
            'title' => 'New Season Update',
            'content' => 'Winter 2024 themes are being added to the database.',
            'type' => 'success',
            'icon' => 'new_releases',
            'priority' => 5,
        ]);

        Announcement::create([
            'title' => 'Community Event',
            'content' => 'Join our Discord for the weekly ranking discussion!',
            'type' => 'event',
            'icon' => 'event',
            'url' => '#',
            'image' => 'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?q=80&w=1000&auto=format&fit=crop',
            'priority' => 15,
        ]);
    }
}
