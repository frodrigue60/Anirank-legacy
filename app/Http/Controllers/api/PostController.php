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
        Auth::guard('sanctum')->user(); // Populate user context for guest-accessible route
        $status = true;

        // Weakly Ranking (3 OP + 3 ED)
        $openings = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images', 'artists:id,name,slug', 'artists.images'])
            ->withAvg('ratings', 'rating')
            ->where('type', 'OP')
            ->whereHas('post', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('ratings_avg_rating')
            ->take(3)
            ->get();

        $endings = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images', 'artists:id,name,slug', 'artists.images'])
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
        $recently = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images'])
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->take(25)
            ->get();

        // Popular Songs (Likes)
        $popular = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images'])
            ->withAvg('ratings', 'rating')
            ->withCount('likes')
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('likes_count')
            ->take(25)
            ->get();

        // Most Viewed Songs
        $viewed = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images'])
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('views')
            ->take(25)
            ->get();

        // Featured Artists
        $featured_artists = Artist::select('id', 'name', 'slug')->with('images')->latest()->take(6)->get();

        // Featured Song (Random)
        $featured_song = Song::withUserInteractions()
            ->with(['post:id,title,slug', 'post.images', 'artists:id,name,slug', 'artists.images'])
            ->withAvg('ratings', 'rating')
            ->whereHas('post', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->inRandomOrder()
            ->first();

        $appendUrls = function ($collection) {
            foreach ($collection as $item) {
                $item->append('average_rating');
                if (isset($item->post)) {
                    $item->post->append(['thumbnail_url', 'banner_url']);
                }
                if (isset($item->artists)) {
                    $item->artists->each->append('avatar_url');
                }
            }

            return $collection;
        };

        if ($featured_song) {
            $featured_song->append('average_rating');
            if ($featured_song->post) {
                $featured_song->post->append(['thumbnail_url', 'banner_url']);
            }
            if ($featured_song->artists) {
                $featured_song->artists->each->append('avatar_url');
            }
        }

        $featured_artists->each->append('avatar_url');

        return response()->json([
            'featured_song' => $featured_song,
            'weakly_ranking' => ['op' => $appendUrls($openings), 'ed' => $appendUrls($endings)],
            'featured_artists' => $featured_artists,
            'recently_added' => $appendUrls($recently),
            'most_popular' => $appendUrls($popular),
            'most_viewed' => $appendUrls($viewed),
        ]);
    }

    public function index(Request $request)
    {
        Auth::guard('sanctum')->user(); // Populate user context for guest-accessible route
        $season_id = $request->season_id;
        $year_id = $request->year_id;
        $name = $request->name;
        $type = $request->type;
        $genre_id = $request->genre_id;
        $sort = $request->sort ?? 'latest';

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
            ->when($type, function ($query, $type) {
                $query->whereHas('format', function ($q) use ($type) {
                    if ($type === 'tv_show') {
                        $q->where('slug', 'tv');
                    } elseif ($type === 'tv_short') {
                        $q->where('slug', 'tv-short');
                    } else {
                        $q->where('slug', $type);
                    }
                });
            })
            ->when($genre_id, function ($query, $genre_id) {
                $query->whereHas('genres', function ($q) use ($genre_id) {
                    $q->where('genres.id', $genre_id);
                });
            })
            ->with(['format:id,name', 'season:id,name', 'year:id,name', 'studios:id,name,slug', 'genres:id,name', 'images'])
            ->with(['songs' => function ($q) {
                $q->withUserInteractions()->withAvg('ratings', 'rating');
            }])
            ->withCount('songs')
            ->when($sort === 'latest', fn ($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'most_themes', fn ($q) => $q->orderByDesc('songs_count'))
            ->when($sort === 'least_themes', fn ($q) => $q->orderBy('songs_count'))
            ->when($sort === 'title', fn ($q) => $q->orderBy('title'))
            ->paginate($request->input('per_page', 18));

        $posts->getCollection()->each(function ($post) {
            $post->append('thumbnail_url');
            $post->average_rating = $post->songs->avg('ratings_avg_rating') ?: 0;
            $post->makeHidden('songs');
        });

        return response()->json($posts);
    }

    public function show(Post $post)
    {
        Auth::guard('sanctum')->user(); // Populate user context for guest-accessible route
        $post->load([
            'year',
            'season',
            'studios',
            'producers',
            'format',
            'genres',
            'externalLinks',
            'songs' => function ($q) {
                $q->withUserInteractions()
                  ->with(['artists', 'artists.images'])
                  ->withAvg('ratings', 'rating');
            },
        ]);

        $post->append(['thumbnail_url', 'banner_url']);

        $post->songs->each(function ($song) {
            $song->append('average_rating');
            if ($song->artists) {
                $song->artists->each->append('avatar_url');
            }
        });

        // Calcular rating promedio del anime basado en sus canciones
        $post->average_rating = $post->songs->avg('ratings_avg_rating') ?: 0;

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
