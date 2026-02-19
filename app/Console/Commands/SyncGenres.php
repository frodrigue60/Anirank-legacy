<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Genre;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class SyncGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync genres for all existing posts from AniList';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::whereNotNull('anilist_id')->get();
        $this->info("Found {$posts->count()} posts to sync.");

        $client = new Client();
        $query = '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    genres
                }
            }
        ';

        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            try {
                $response = $client->post('https://graphql.anilist.co', [
                    'json' => [
                        'query' => $query,
                        'variables' => ['id' => $post->anilist_id],
                    ]
                ]);

                $data = json_decode($response->getBody())->data->Media;
                $genreIds = [];

                foreach ($data->genres as $genreName) {
                    $genre = Genre::firstOrCreate(
                        ['slug' => Str::slug($genreName)],
                        ['name' => $genreName, 'slug' => Str::slug($genreName)]
                    );
                    $genreIds[] = $genre->id;
                }

                $post->genres()->sync($genreIds);
            } catch (\Exception $e) {
                $this->error("\nError syncing post {$post->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nGenres synced successfully!");
    }
}
