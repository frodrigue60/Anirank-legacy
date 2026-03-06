<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use App\Models\SongVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function toggleSong(Song $song)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('warning', 'Please login');
        }

        $user = Auth::user();
        $results = $song->favorites()->toggle($user->id);
        $isFavorite = count($results['attached']) > 0;

        return redirect()->back()->with('success', $isFavorite ? 'Song added to favorites' : 'Song removed from favorites');
    }

    public function toggleArtist(Artist $artist)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('warning', 'Please login');
        }

        $user = Auth::user();
        $results = $artist->favoritedBy()->toggle($user->id);
        $isFavorite = count($results['attached']) > 0;

        return redirect()->back()->with('success', $isFavorite ? 'Artist added to favorites' : 'Artist removed from favorites');
    }

    public function toggle(SongVariant $variant)
    {
        // Mantener compatibilidad pero redirigir al Song base
        return $this->toggleSong($variant->song);
    }
}
