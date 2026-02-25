<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class GlobalBottomPlayer extends Component
{
    public $song;
    public $isVisible = false;
    public $isPlaying = false;
    public $progress = 0;
    public $currentTime = '0:00';
    public $duration = '0:00';
    public $hasVideoLoaded = false;

    #[On('playSong')]
    public function handlePlaySong($songId)
    {
        Log::info("GlobalBottomPlayer: Requested song ID $songId");
        $this->song = Song::with(['post', 'artists', 'firstSongVariant.video'])->find($songId);

        if (!$this->song) {
            Log::error("GlobalBottomPlayer: Song not found $songId");
            return;
        }

        $this->calculateScore();
        $this->isPlaying = true;
        $this->isVisible = true;
        $this->hasVideoLoaded = true;

        // Satisfy user requested logic: check first variant then fall back to direct videos
        $video = ($this->song->firstSongVariant && $this->song->firstSongVariant->video)
            ? $this->song->firstSongVariant->video
            : $this->song->videos()->first();

        // Build thumbnail URL from the post's thumbnail
        $thumbnailUrl = null;
        if ($this->song->post->thumbnail && Storage::disk()->exists($this->song->post->thumbnail)) {
            $thumbnailUrl = Storage::url($this->song->post->thumbnail);
        } elseif ($this->song->post->thumbnail_src) {
            $thumbnailUrl = $this->song->post->thumbnail_src;
        }

        if ($video) {
            $videoUrl = $video->video_src;
            if ($video->isLocal()) {
                $videoUrl = Storage::disk($video->disk)->url($video->video_src);
            } else if ($video->isEmbed()) {
                // video_src may contain raw <iframe> or <embed> HTML; extract the src URL
                $raw = $video->video_src;
                if (preg_match('/(?:src=["\'])([^"\']*)/', $raw, $matches)) {
                    $videoUrl = $matches[1];
                } else {
                    $videoUrl = $raw; // Already a plain URL
                }
            }

            Log::info("GlobalBottomPlayer: Loading video of type {$video->type} with URL: $videoUrl");

            $this->dispatch(
                'song-loaded',
                url: $videoUrl,
                type: $video->type, // 'file' or 'embed'
                title: $this->song->name,
                anime: $this->song->post->title,
                artists: $this->song->artists->pluck('name')->join(', '),
                thumbnail: $thumbnailUrl
            );
        } else {
            Log::warning("GlobalBottomPlayer: No video found for song ID $songId");
        }
    }

    private function calculateScore()
    {
        if (!$this->song) return;

        $format = Auth::user()?->score_format ?? 'POINT_100';
        $this->song->formattedScore = $this->song->formattedAvgScore($format);
    }

    public function togglePlay()
    {
        $this->isPlaying = !$this->isPlaying;
        $this->dispatch('toggle-playback', ['playing' => $this->isPlaying]);
    }

    public function render()
    {
        return view('livewire.global-bottom-player');
    }
}
