<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Post;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function home()
    {
        // $user = Auth::guard('sanctum')->user();
        $status = true;

        // Weakly Ranking (3 OP + 3 ED)
        $openings = Song::with(['post:id,title', 'artists:id,name'])
            ->withAvg('ratings', 'rating')
            ->where('type', 'OP')
            ->whereHas('post', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('ratings_avg_rating')
            ->take(3)
            ->get();

        $endings = Song::with(['post:id,title', 'artists:id,name'])
            ->withAvg('ratings', 'rating')
            ->where('type', 'ED')
            ->whereHas('post', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('ratings_avg_rating')
            ->take(3)
            ->get();

        $weaklyRanking = $openings->concat($endings);

        // Recently Added Songs
        $recently = Song::with(['post:id,title'])
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->take(25)
            ->get();

        // Popular Songs (Likes)
        $popular = Song::with(['post:id,title'])
            ->withCount('likes')
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('likes_count')
            ->take(25)
            ->get();

        // Most Viewed Songs
        $viewed = Song::with(['post:id,title'])
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('views')
            ->take(25)
            ->get();

        // Featured Artists
        $featured_artists = Artist::select('id', 'name')->latest()->take(6)->get();

        // Featured Song (Random)
        $featured_song = Song::with(['post:id,title', 'artists:id,name'])
            ->whereHas('post', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->inRandomOrder()
            ->first();

        return response()->json([
            'featured_song' => $featured_song,
            'weakly_ranking' => $weaklyRanking,
            'featured_artists' => $featured_artists,
            'recently_added' => $recently,
            'most_popular' => $popular,
            'most_viewed' => $viewed,
        ]);
    }

    public function index(Request $request)
    {
        // $q = $request->q;
        $season_id = $request->season_id;
        $year_id = $request->year_id;
        $name = $request->name;
        $format_id = $request->format_id;

        $status = true;

        $posts = Post::where('status', $status)
            ->when($season_id, function ($query, $season_id) {
                $query->where('season_id', $season_id);
            })
            ->when($year_id, function ($query, $year_id) {
                $query->where('year_id', $year_id);
            })
            ->when($name, function ($query, $name) {
                $query->where('title', 'LIKE', '%'.$name.'%');
            })
            ->when($format_id, function ($query, $format_id) {
                $query->where('format_id', $format_id);
            })
            ->paginate(15);

        return response()->json($posts);
    }

    public function show(Post $post)
    {
        return response()->json($post);
    }

    public function globalSearch(Request $request)
    {
        $q = $request->q;
        $posts = Post::where('title', 'LIKE', '%'.$q.'%')->limit(5)->get(['title', 'slug']);

        $artists = Artist::where('name', 'LIKE', '%'.$q.'%')->limit(5)->get(['name', 'slug']);

        $users = User::where('name', 'LIKE', '%'.$q.'%')->limit(5)->get(['name', 'slug']);

        return response()->json([
            'posts' => $posts,
            'artists' => $artists,
            'users' => $users,
        ]);
    }
}
