<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Producer;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->name;
        $sort = $request->sort ?? 'most_animes';

        $query = Producer::query()->whereHas('posts', function ($q) {
            $q->where('status', true);
        })->withCount(['posts' => function ($q) {
            $q->where('status', true);
        }]);

        // Load posts to get a banner image (taking the latest one)
        $query->with(['posts' => function ($q) {
            $q->where('status', true)->latest();
        }]);

        if ($name) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'least_animes':
                $query->orderBy('posts_count', 'asc');
                break;
            case 'most_animes':
            default:
                $query->orderBy('posts_count', 'desc');
                break;
        }

        $producers = $query->paginate(18);

        foreach ($producers as $producer) {
            $featured = $producer->posts->first();
            if ($featured) {
                $featured->append('banner_url');
                $producer->featured_image = $featured->banner_url;
                $producer->featured_title = $featured->title;
            } else {
                $producer->featured_image = null;
                $producer->featured_title = null;
            }
            // Remove the collection to keep the response clean
            unset($producer->posts);
        }

        return response()->json([
            'producers' => $producers,
        ]);
    }

    public function show(Producer $producer)
    {
        return response()->json([
            'producer' => $producer,
        ]);
    }

    public function animes(Request $request, Producer $producer)
    {
        $status = true;
        $format_id = $request->format_id;
        $name = $request->name;
        $year_id = $request->year_id;
        $season_id = $request->season_id;
        $sort = $request->sort ?? 'title';

        $query = Post::where('status', $status)
            ->whereHas('producers', function ($query) use ($producer) {
                $query->where('producers.id', $producer->id);
            })
            ->with(['format:id,name', 'season:id,name', 'year:id,name', 'images'])
            ->with(['songs' => function ($q) {
                $q->withAvg('ratings', 'rating');
            }])
            ->withCount('songs')
            ->when($name, function ($query) use ($name) {
                $query->where('title', 'like', '%'.$name.'%');
            })
            ->when($year_id, function ($query) use ($year_id) {
                $query->where('year_id', $year_id);
            })
            ->when($season_id, function ($query) use ($season_id) {
                $query->where('season_id', $season_id);
            })
            ->when($format_id, function ($query) use ($format_id) {
                $query->where('format_id', $format_id);
            });

        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('title', 'desc');
        } elseif ($sort === 'most_themes') {
            $query->orderBy('songs_count', 'desc');
        } elseif ($sort === 'least_themes') {
            $query->orderBy('songs_count', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(18);

        $posts->getCollection()->each(function ($post) {
            $post->append('thumbnail_url');
            $post->average_rating = $post->songs->avg('ratings_avg_rating') ?: 0;
            $post->makeHidden('songs');
        });

        return response()->json([
            'posts' => $posts,
            'producer' => $producer,
        ]);
    }
}
