<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\AnimeController;
use App\Models\Anime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAnimeAnilistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $animeId;

    /**
     * Create a new job instance.
     */
    public function __construct($animeId)
    {
        $this->animeId = $animeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $anime = Anime::find($this->animeId);

        if (! $anime) {
            return;
        }

        $controller = new AnimeController;
        $controller->forceUpdateSilently($anime);
    }
}
