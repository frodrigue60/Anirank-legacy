<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Artist;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateArtistAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artists:generate-avatars {--force : Redownload even if they already have one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and store UI-Avatars for existing artists who lack a thumbnail.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Artist::query();

        if (!$this->option('force')) {
            $query->whereDoesntHave('images', function ($q) {
                $q->where('type', 'thumbnail');
            });
        }

        $artists = $query->get();
        $total = $artists->count();

        if ($total === 0) {
            $this->info('No artists found without an avatar.');
            return 0;
        }

        $this->info("Found {$total} artists to process.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($artists as $artist) {
            try {
                $name = urlencode($artist->name);
                $url = "https://ui-avatars.com/api/?name={$name}&color=fff&background=random&size=512";

                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $file_name = $artist->slug . '-avatar-' . time() . '.png';
                    $path = 'artists/' . $file_name;

                    Storage::disk()->put($path, $response->body());
                    $artist->updateOrCreateImage($path, 'thumbnail');
                }
            } catch (\Exception $e) {
                $this->error("\nFailed to process artist {$artist->id}: " . $e->getMessage());
                Log::error("Artisan command failed for artist {$artist->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nDone! Avatars generated for existing artists.");

        return 0;
    }
}
