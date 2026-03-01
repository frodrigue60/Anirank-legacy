<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    /**
     * Display a listing of the public tournaments.
     */
    public function index()
    {
        $tournaments = Tournament::whereIn('status', ['active', 'completed'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('tournaments.index', compact('tournaments'));
    }

    /**
     * Display the specified tournament bracket.
     */
    public function show(Tournament $tournament)
    {
        // Require the tournament to not be in draft status for public view
        if ($tournament->status === 'draft') {
            abort(404);
        }

        return view('tournaments.show', compact('tournament'));
    }
}
