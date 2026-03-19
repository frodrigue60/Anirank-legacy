<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Song;
use App\Models\Rating;
use App\Models\Artist;
use App\Models\Anime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Display the global statistics page.
     */
    public function index()
    {
        // 1. Database Totals
        $totals = [
            'users' => User::count(),
            'songs' => Song::count(),
            'animes' => Anime::count(),
            'artists' => Artist::count(),
            'ratings' => Rating::count(),
        ];

        // 2. User Growth (Last 12 Months)
        $userGrowth = User::select(
            DB::raw('COUNT(id) as count'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 3. Rating Activity (Last 12 Months)
        $ratingActivity = Rating::select(
            DB::raw('COUNT(id) as count'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 4. Level Distribution
        $levelDistribution = User::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        // 5. Voting Habits (Star Distribution)
        $votingHabits = Rating::select('rating as score', DB::raw('count(*) as count'))
            ->groupBy('score')
            ->orderBy('score')
            ->get();

        return view('public.stats.index', compact(
            'totals',
            'userGrowth',
            'ratingActivity',
            'levelDistribution',
            'votingHabits'
        ));
    }
}
