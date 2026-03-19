<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\Anime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\XpService;

class PlaylistController extends Controller
{
    protected $xpService;

    public function __construct(XpService $xpService)
    {
        $this->xpService = $xpService;
    }
    public function index(Request $request)
    {
        $query = Playlist::query()
            ->withCount('songs')
            ->with(['user', 'songs' => function ($query) {
                $query->with('anime')->limit(1);
            }]);

        if ($request->has('owned') && Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('is_public', true);
        }

        $playlists = $query->latest()->paginate(12);

        return view('public.playlists.index', compact('playlists'));
    }

    public function create()
    {
        return view('public.playlists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $playlist = new Playlist();
        $playlist->name = $request->input('name');
        $playlist->description = $request->input('description');
        $playlist->user_id = Auth::id();
        $playlist->save();

        $this->xpService->award(Auth::user(), 'create_playlist', [
            'playlist_id' => $playlist->id
        ]);

        $message = 'Playlist created successfully.';

        return redirect()->route('playlists.index')->with('success', $message);
    }

    public function show(Playlist $playlist)
    {
        if (!$playlist->is_public && (!Auth::check() || Auth::id() !== $playlist->user_id)) {
            abort(403, 'This playlist is private.');
        }

        $playlist->load(['songs.anime', 'songs.songVariants.video', 'songs.artists']);

        $queue = $playlist->songs->map(function ($song) {
            // 1. Tomar la primera variante
            $firstVariant = $song->songVariants->first();

            if (!$firstVariant) {
                return null;
            }

            // 2. Obtener el video asociado
            $video = $firstVariant->video;

            if (!$video) {
                return null;
            }

            // 3. Determinar el thumbnail (preferencia al anime)
            $thumbnailUrl = $song->anime ? $song->anime->thumbnail_url : asset('resources/images/song_cover.png');
            if (!$song->anime && $song->thumbnail) {
                $thumbnailUrl = $song->thumbnail;
            }

            // 4. Construir item
            $typeLabels = [
                'OP' => 'OPENING',
                'ED' => 'ENDING',
                'INS' => 'INSERT',
                'OTH' => 'OTHER',
            ];
            $formattedType = ($typeLabels[$song->type] ?? 'THEME') . ' ' . ($song->theme_num ?? '');

            return [
                'song_id'         => $song->id,
                'title'           => $song->name,
                'artists'         => $song->artists->pluck('name')->join(', '),
                'anime'           => $song->anime->title ?? 'Unknown Anime',
                'song_type'       => trim($formattedType),
                'average_rating'  => number_format($song->averageRating, 1) ?? 'N/A',
                'variant_id'      => $firstVariant->id,
                'variant_quality' => $firstVariant->quality ?? 'unknown',
                'video_id'        => $video->id,
                'type'            => $video->type,
                'video_url'       => $video->type === 'embed'
                    ? $video->embed_url
                    : $video->local_url,
                'duration'        => $video->duration ?? 0,
                'thumbnail'       => $thumbnailUrl,
            ];
        })
            ->filter()
            ->values();

        return view('public.playlists.show', compact('playlist', 'queue'));
    }

    public function edit(Playlist $playlist)
    {
        return view('public.playlists.edit', compact('playlist'));
    }

    public function update(Request $request, Playlist $playlist)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $playlist->name = $request->input('name');
        $playlist->description = $request->input('description');
        $playlist->save();

        $message = 'Playlist updated successfully.';

        return redirect()->route('playlists.index')->with('success', $message);
    }

    public function destroy(Playlist $playlist)
    {

        $playlist->delete();

        $message = 'Playlist deleted successfully.';

        return redirect()->route('playlists.index')->with('success', $message);
    }
}
