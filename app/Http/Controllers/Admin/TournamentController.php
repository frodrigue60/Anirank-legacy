<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Tournament;
use App\Models\TournamentMatchup;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::latest()->paginate(15);

        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'required|in:2,4,8,16,32',
            'type_filter' => 'nullable|string|in:OP,ED',
        ]);

        $baseSlug = \Illuminate\Support\Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        while (Tournament::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $validated['slug'] = $slug;

        Tournament::create($validated);

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament created successfully.');
    }

    public function show(Tournament $tournament)
    {
        $tournament->load('matchups.song1', 'matchups.song2', 'matchups.winner', 'winner');

        return view('admin.tournaments.show', compact('tournament'));
    }

    public function edit(Tournament $tournament)
    {
        return view('admin.tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_filter' => 'nullable|string|in:OP,ED',
            'status' => 'required|in:draft,active,completed',
        ]);

        if ($tournament->name !== $validated['name']) {
            $baseSlug = \Illuminate\Support\Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            while (Tournament::where('slug', $slug)->where('id', '!=', $tournament->id)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $tournament->update($validated);

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament updated successfully.');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament deleted successfully.');
    }

    public function seed(Request $request, Tournament $tournament)
    {
        if ($tournament->matchups()->count() > 0) {
            return back()->with('error', 'Tournament already has matchups generated.');
        }

        // Fetch top rated songs to seed the tournament
        $songs = Song::withCount('ratings')
            ->when($tournament->type_filter, function ($q) use ($tournament) {
                return $q->where('type', $tournament->type_filter);
            })
            ->orderBy('ratings_count', 'desc')
            ->take($tournament->size)
            ->get();

        if ($songs->count() < $tournament->size) {
            return back()->with('error', "Not enough songs in the database. Required: {$tournament->size}, found: {$songs->count()}.");
        }

        // Shuffle for randomness or seed them by popularity (1 vs 16, 2 vs 15)
        // Let's do a simple shuffle for now
        $songs = $songs->shuffle()->values();

        $numberOfMatches = $tournament->size / 2;
        $round = $tournament->size;

        $tournament->current_round = $round;
        $tournament->status = 'active';
        $tournament->started_at = now();
        $tournament->save();

        for ($i = 0; $i < $numberOfMatches; $i++) {
            TournamentMatchup::create([
                'tournament_id' => $tournament->id,
                'round' => $round,
                'position' => $i + 1,
                'song1_id' => $songs[$i * 2]->id,
                'song2_id' => $songs[$i * 2 + 1]->id,
                'ends_at' => now()->addDays(2), // Each round lasts 2 days by default
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Tournament seeded successfully and is now active!');
    }

    public function forceRound(Tournament $tournament)
    {
        if ($tournament->status !== 'active') {
            return redirect()->back()->with('error', 'Only active tournaments can be forced to end the current round.');
        }

        // expire all active matchups for this tournament
        $tournament->matchups()
            ->where('is_active', true)
            ->update(['ends_at' => now()]);

        // Trigger the progression command
        \Illuminate\Support\Facades\Artisan::call('tournaments:process');

        return redirect()->back()->with('success', 'Current round has been finalized successfully.');
    }
}
