<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Song;
use App\Models\Anime;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SongDetail extends Component
{
    public $song;
    public $anime;
    public $currentVariant;
    public $comments;
    public $relatedSongs;

    // Comment Form
    #[Validate('required|min:3|max:1000')]
    public $commentBody = '';

    #[Validate('required|min:3|max:1000')]
    public $replyBody = '';

    public $replyingTo = null;
    public $editingCommentId = null;

    #[Validate('required|min:3|max:1000')]
    public $editingBody = '';

    #[Validate('required|min:3|max:50')]
    public $newPlaylistName = '';

    // Playlist State
    public $showPlaylistModal = false;
    public $userPlaylists = [];

    // Rating State
    public $showRatingModal = false;
    public $ratingValue = 0;

    public function mount(Song $song, Anime $anime)
    {
        $this->song = $song;
        $this->anime = $anime;

        $this->loadVariant();
        $this->loadComments();
        $this->loadRelated();
        $this->calculateScore();
    }

    private function calculateScore()
    {
        $song = $this->song;
        $format = Auth::user()?->score_format ?? 'POINT_100';

        $denominatorMap = [
            'POINT_100'        => 100,
            'POINT_10_DECIMAL' => 10,
            'POINT_10'         => 10,
            'POINT_5'          => 5,
        ];

        $song->rawScore      = round($song->averageRating, 1);
        $song->formattedScore = $song->score ?? $song->formattedAvgScore($format);
        $song->scoreString   = $song->formattedScore . '/' . ($denominatorMap[$format] ?? 100);
    }

    public function loadVariant()
    {
        $this->currentVariant = $this->song->songVariants->sortBy('version_number')->first();
    }

    public function loadComments()
    {
        $this->comments = Comment::with(['user', 'user.badges', 'replies' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'replies.user'])
            ->where('song_id', $this->song->id)
            ->where('parent_id', null)
            ->orderByDesc('created_at')
            ->get();
    }

    public function loadRelated()
    {
        $this->relatedSongs = $this->anime->songs()
            ->where('id', '!=', $this->song->id)
            ->with(['artists'])
            ->get();
    }

    public function switchVariant($variantId)
    {
        $this->currentVariant = $this->song->songVariants->find($variantId);
        $this->dispatch('video-changed', src: $this->getVideoUrl(), isEmbed: $this->isCurrentEmbed());
    }

    public function getVideoUrl()
    {
        if (!$this->currentVariant || !$this->currentVariant->video) {
            return '';
        }

        $video = $this->currentVariant->video;

        if ($video->isEmbed()) {
            // embed_code contains full <iframe> HTML, extract the src attribute
            if (preg_match('/src=["\']([^"\']+)["\']/', $video->embed_code, $matches)) {
                $url = $matches[1];
                // Append autoplay parameter
                $separator = str_contains($url, '?') ? '&' : '?';
                return $url . $separator . 'autoplay=1';
            }
            return $video->embed_code;
        }

        // File type
        if ($video->video_src && Storage::disk($video->disk)->exists($video->video_src)) {
            return Storage::disk($video->disk)->url($video->video_src);
        }

        return $video->video_src ?? '';
    }

    public function isCurrentEmbed()
    {
        return $this->currentVariant
            && $this->currentVariant->video
            && $this->currentVariant->video->isEmbed();
    }

    public function toggleLike()
    {
        if (!Auth::check()) return redirect()->route('login');
        $this->toggleReaction(1);
    }

    public function toggleDislike()
    {
        if (!Auth::check()) return redirect()->route('login');
        $this->toggleReaction(-1);
    }

    private function toggleReaction($type)
    {
        $userId = Auth::id();
        $existing = $this->song->reactions()->where('user_id', $userId)->first();
        $typeName = $type === 1 ? 'like' : 'dislike';

        if ($existing) {
            if ($existing->pivot->type == $type) {
                // Toggle off
                $this->song->reactions()->detach($userId);
                $this->dispatch('toast', type: 'info', message: "Removed $typeName");
            } else {
                // Update type
                $this->song->reactions()->updateExistingPivot($userId, ['type' => $type]);
                $this->dispatch('toast', type: 'success', message: ucfirst($typeName) . "d the song");
            }
        } else {
            // New reaction
            $this->song->reactions()->attach($userId, ['type' => $type]);
            $this->dispatch('toast', type: 'success', message: ucfirst($typeName) . "d the song");
        }

        $this->song->updateReactionCounters();
        $this->song->refresh();
    }

    public function toggleFavorite()
    {
        if (!Auth::check()) return redirect()->route('login');

        $userId = Auth::id();
        $results = $this->song->favorites()->toggle($userId);
        
        $isFavorite = count($results['attached']) > 0;
        
        $this->dispatch('toast', 
            type: $isFavorite ? 'success' : 'info', 
            message: $isFavorite ? 'Added to favorites!' : 'Removed from favorites'
        );

        $this->song->refresh();
    }

    public function setReplyTo($commentId)
    {
        if (!Auth::check()) return redirect()->route('login');
        $this->replyingTo = $commentId;
        $this->replyBody = '';
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyBody = '';
    }

    public function postComment()
    {
        if (!Auth::check()) return redirect()->route('login');

        if ($this->replyingTo) {
            $this->validateOnly('replyBody');

            $this->song->comments()->create([
                'user_id' => Auth::id(),
                'content' => $this->replyBody,
                'parent_id' => $this->replyingTo
            ]);

            $this->replyBody = '';
            $this->replyingTo = null;
        } else {
            $this->validateOnly('commentBody');

            $this->song->comments()->create([
                'user_id' => Auth::id(),
                'content' => $this->commentBody
            ]);

            $this->commentBody = '';
        }

        $this->loadComments();
        $this->dispatch('comment-posted');
        $this->dispatch('toast', type: 'success', message: 'Comment posted successfully!');
    }

    public function deleteComment($commentId)
    {
        if (!Auth::check()) return redirect()->route('login');

        $comment = Comment::find($commentId);

        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->isAdmin())) {
            $comment->delete();
            $this->loadComments();
            $this->dispatch('toast', type: 'success', message: 'Comment deleted.');
        }
    }

    public function startEditing($commentId)
    {
        if (!Auth::check()) return redirect()->route('login');

        $comment = Comment::find($commentId);
        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->isAdmin())) {
            $this->editingCommentId = $commentId;
            $this->editingBody = $comment->content;
            $this->cancelReply();
        }
    }

    public function cancelEditing()
    {
        $this->editingCommentId = null;
        $this->editingBody = '';
    }

    public function updateComment()
    {
        if (!Auth::check()) return redirect()->route('login');

        $comment = Comment::find($this->editingCommentId);
        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->isAdmin())) {
            $this->validateOnly('editingBody');

            $comment->update(['content' => $this->editingBody]);

            $this->editingCommentId = null;
            $this->editingBody = '';
            $this->loadComments();
            $this->dispatch('toast', type: 'success', message: 'Comment updated!');
        }
    }

    public function openPlaylistModal()
    {
        if (!Auth::check()) return redirect()->route('login');

        $this->userPlaylists = Auth::user()->playlists()->withCount(['songs' => function ($query) {
            $query->where('song_id', $this->song->id);
        }])->get();

        $this->showPlaylistModal = true;
    }

    public function createPlaylist()
    {
        $this->validateOnly('newPlaylistName');

        $playlist = Auth::user()->playlists()->create([
            'name' => $this->newPlaylistName,
            'is_public' => true
        ]);

        $playlist->songs()->attach($this->song->id, ['position' => 1]);

        $this->newPlaylistName = '';
        $this->openPlaylistModal();
        $this->dispatch('toast', type: 'success', message: 'Playlist created successfully!');
    }

    public function togglePlaylist($playlistId)
    {
        $playlist = Auth::user()->playlists()->find($playlistId);

        if ($playlist->songs()->where('song_id', $this->song->id)->exists()) {
            $playlist->songs()->detach($this->song->id);
            $this->dispatch('toast', type: 'info', message: 'Removed from playlist');
        } else {
            $maxPos = $playlist->songs()->max('position') ?? 0;
            $playlist->songs()->attach($this->song->id, ['position' => $maxPos + 1]);
            $this->dispatch('toast', type: 'success', message: 'Added to playlist');
        }

        $this->openPlaylistModal();
    }

    public function openRatingModal()
    {
        if (!Auth::check()) return redirect()->route('login');

        $rating = $this->song->ratings()->where('user_id', Auth::id())->first();
        $this->ratingValue = $rating ? $rating->rating : 0;

        $this->showRatingModal = true;
    }

    public function rate($value = null)
    {
        if (!Auth::check()) return redirect()->route('login');

        try {
            $value = $value ?? $this->ratingValue;

            if ($value < 0 || $value > 100) {
                throw new \Exception('Invalid rating value.');
            }

            $this->song->rate($value, Auth::id());
            $this->calculateScore();
            $this->showRatingModal = false;

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Rating Saved!',
                description: "You rated {$this->song->name} with {$value} points."
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Error saving rating',
                description: $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.song-detail');
    }
}
