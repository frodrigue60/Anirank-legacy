<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Anime;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = $request->query('q');

        if (! $query || strlen($query) < 2) {
            return response()->json([
                'animes' => [],
                'artists' => [],
                'users' => [],
            ]);
        }

        // Limit results per category to keep response fast and concise
        $limit = 5;

        // Search Animes
        $animes = Anime::where('title', 'LIKE', "%{$query}%")
            ->select('id', 'title', 'slug')
            ->with('images')
            ->limit($limit)
            ->get()
            ->append('thumbnail_url');

        // Search Artists
        $artists = Artist::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'slug')
            ->with('images')
            ->limit($limit)
            ->get()
            ->append('avatar_url');

        // Search Users
        $users = User::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'slug')
            ->with('images')
            ->limit($limit)
            ->get()
            ->append('avatar_url');

        return response()->json([
            'animes' => $animes,
            'artists' => $artists,
            'users' => $users,
        ]);
    }
}
