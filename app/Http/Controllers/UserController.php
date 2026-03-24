<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {}

    public function create()
    {
        return abort(404);
    }

    public function store(Request $request)
    {
        return abort(404);
    }

    public function show(User $user)
    {
        return view('public.users.show', compact('user'));
    }

    public function edit($id)
    {
        return abort(404);
    }

    public function update(Request $request, $id)
    {
        return abort(404);
    }

    public function destroy($id)
    {
        return abort(404);
    }

    public function settings()
    {
        $score_formats = [
            ['name' => ' 100 Point (55/100)', 'value' => 'POINT_100'],
            ['name' => '10 Point Decimal (5.5/10)', 'value' => 'POINT_10_DECIMAL'],
            ['name' => '10 Point (5/10)', 'value' => 'POINT_10'],
            ['name' => '5 Star (3/5)', 'value' => 'POINT_5'],
        ];

        $user = Auth::user();

        return view('public.users.settings', compact('score_formats', 'user'));
    }

    public function sortSongs($sort, $songs)
    {
        switch ($sort) {
            case 'title':
                $songs = $songs->sortBy(function ($song) {
                    return $song->anime->title;
                });

                return $songs;
                break;
            case 'averageRating':
                $songs = $songs->sortByDesc('averageRating');

                return $songs;
            case 'view_count':
                $songs = $songs->sortByDesc('view_count');

                return $songs;

            case 'likeCount':
                $songs = $songs->sortByDesc('likeCount');

                return $songs;
                break;
            case 'recent':
                $songs = $songs->sortByDesc('created_at');

                return $songs;
                break;

            default:
                $songs = $songs->sortBy(function ($song) {
                    return $song->anime->title;
                });

                return $songs;
                break;
        }
    }

    public function setScore($songs, $score_format)
    {
        $songs->each(function ($song) use ($score_format) {
            $song->score = $song->formattedAvgScore($score_format);
            $song->user_score = isset($song->rating)
                ? $song->formattedUserScore($score_format, Auth::id())
                : null;
        });

        return $songs;
    }

    public function uploadProfilePic(Request $request)
    {
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'mimes:png,jpg,jpeg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect(route('users.profile'))->with('error', 'Invalid file format or size.');
            }

            $user = Auth::user();

            $extension = $request->image->extension();
            $file_name = $user->slug.'-'.time().'.'.$extension;
            $path = 'profile/'.$file_name;

            $storedPath = $request->image->storeAs('profile', $file_name);

            if ($storedPath) {
                $user->updateOrCreateImage($storedPath, 'avatar');

                return redirect(route('users.profile'))->with('success', 'Profile picture updated successfully!');
            }
        }

        return redirect(route('users.profile'))->with('warning', 'File not found');
    }

    public function uploadBannerPic(Request $request)
    {
        if ($request->hasFile('banner')) {
            $validator = Validator::make($request->all(), [
                'banner' => 'mimes:png,jpg,jpeg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect(route('users.profile'))->with('error', 'Invalid file format or size.');
            }

            $user = Auth::user();

            $extension = $request->banner->extension();
            $file_name = $user->slug.'-'.time().'.'.$extension;
            $path = 'banner/'.$file_name;

            $storedPath = $request->banner->storeAs('banner', $file_name);

            if ($storedPath) {
                $user->updateOrCreateImage($storedPath, 'banner');

                return redirect(route('users.profile'))->with('success', 'Banner updated successfully!');
            }
        }

        return redirect(route('users.profile'))->with('warning', 'File not found');
    }

    public function changeScoreFormat(Request $request)
    {
        if ($request->score_format == 'null') {
            return redirect()->back()->with('warning', 'score method not changed');
        }

        $validator = Validator::make($request->all(), [
            'score_format' => 'required|in:POINT_100,POINT_10_DECIMAL,POINT_10,POINT_5',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->with('error', '¡Ooops!');
        }

        if (Auth::check()) {
            $user = Auth::user();
            $user = User::find($user->id);
            $user->score_format = $request->score_format;
            $user->update();

            return redirect()->back()->with('success', 'score method changed successfully');
        } else {
            return redirect(route('login'));
        }
    }

    public function SeasonsYears($tags)
    {
        $tagNames = [];
        $tagYears = [];

        for ($i = 0; $i < count($tags); $i++) {
            [$name, $year] = explode(' ', $tags[$i]->name);

            if (! in_array($year, $tagNames)) {
                $years[] = ['name' => $year, 'value' => $year];
                $tagNames[] = $year; // Agregamos el año al array de nombres para evitar duplicados
            }

            if (! in_array($name, $tagYears)) {
                $seasons[] = ['name' => $name, 'value' => $name];
                $tagYears[] = $name; // Agregamos el año al array de nombres para evitar duplicados
            }
        }

        $data = [
            'years' => $years,
            'seasons' => $seasons,
        ];

        return $data;
    }

    public function filterTypesSortChar()
    {
        $filters = [
            ['name' => 'All', 'value' => 'all'],
            ['name' => 'Only Rated', 'value' => 'rated'],
        ];

        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];

        $sortMethods = [
            ['name' => 'Recent', 'value' => 'recent'],
            ['name' => 'Title', 'value' => 'title'],
            ['name' => 'Score', 'value' => 'averageRating'],
            ['name' => 'Views', 'value' => 'view_count'],
            ['name' => 'Popular', 'value' => 'likeCount'],
        ];

        $characters = range('A', 'Z');

        $data = [
            'filters' => $filters,
            'types' => $types,
            'sortMethods' => $sortMethods,
            'characters' => $characters,
        ];

        return $data;
    }
}
