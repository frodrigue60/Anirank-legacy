<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $songId = $request->query('song_id') ?? $request->input('song_id');
        $user = auth('sanctum')->user();

        $query = Playlist::with(['user', 'songs.post'])->withCount('songs');

        if ($request->has('mine') && $user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('is_public', true);
        }

        $playlists = $query->latest()->get();

        if ($songId) {
            $playlistsWithSong = DB::table('playlist_song')
                ->where('song_id', $songId)
                ->whereIn('playlist_id', $playlists->pluck('id'))
                ->pluck('playlist_id')
                ->toArray();

            $playlists = $playlists->map(function ($playlist) use ($playlistsWithSong) {
                $playlist->is_in_playlist = in_array($playlist->id, $playlistsWithSong);
                return $playlist;
            });
        }

        return response()->json([
            'playlists' => $playlists,
            'song_id'   => $songId,
            'message'   => 'Playlists retrieved successfully',
            'status'    => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        $playlist = Playlist::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'is_public' => $request->boolean('is_public', false),
        ]);

        return response()->json([
            'playlist' => $playlist,
            'message' => 'Playlist created successfully',
            'status' => 201
        ], 201);
    }

    public function update(Request $request, Playlist $playlist)
    {
        $this->authorize('update', $playlist);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        $playlist->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->boolean('is_public', $playlist->is_public),
        ]);

        return response()->json([
            'playlist' => $playlist,
            'message' => 'Playlist updated successfully',
            'status' => 200
        ], 200);
    }

    public function show(Playlist $playlist)
    {
        $user = auth('sanctum')->user();
        if (!$playlist->is_public) {
            if (!$user || $user->id !== $playlist->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $playlist->load([
            'songs' => function ($q) {
                $q->withUserInteractions()
                  ->with(['post', 'artists', 'songVariants.video']);
            },
        ]);

        $playlist->songs->each(function ($song) {
            if ($song->post) {
                $song->post->append(['thumbnail_url', 'banner_url']);
            }
            if ($song->artists) {
                $song->artists->each->append('avatar_url');
            }
            $song->songVariants->each(function ($variant) {
                if ($variant->video) {
                    $variant->video->append(['embed_url', 'local_url']);
                }
            });
        });

        return response()->json([
            'playlist' => $playlist,
            'message' => 'Playlist retrieved successfully',
        ], 200);
    }

    public function destroy(Playlist $playlist)
    {
        $this->authorize('delete', $playlist);
        $playlist->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Playlist eliminada']);
        }

        return back()->with('success', 'Playlist eliminada');
    }

    public function toggleSong(Request $request, Playlist $playlist)
    {
        try {
            $request->validate([
                'song_id' => 'required|exists:songs,id'
            ]);

            $songId = $request->song_id;
            $exists = $playlist->songs()->where('song_id', $songId)->exists();

            if ($exists) {
                $playlist->songs()->detach($songId);
                $action = 'removed';
                $message = 'Post removido de la playlist correctamente';
            } else {
                $playlist->songs()->attach($songId);
                $action = 'added';
                $message = 'Post agregado a la playlist correctamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'playlist_id' => $playlist->id,
                    'song_id' => $songId,
                    'action' => $action,
                    'in_playlist' => !$exists
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userPlaylists(Request $request, \App\Models\User $user)
    {
        $query = $user->playlists()->with(['songs.post'])->withCount('songs');

        // Solo mostrar privadas si es el propio usuario autenticado
        $currentUser = auth('sanctum')->user();
        if (!$currentUser || $currentUser->id !== $user->id) {
            $query->where('is_public', true);
        }

        $playlists = $query->latest()->get();

        return response()->json([
            'playlists' => $playlists,
            'message' => 'User playlists retrieved successfully',
        ], 200);
    }
}
