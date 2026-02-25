<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SeasonController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
        ];
        $seasons = Season::all();

        return view('admin.seasons.index', compact('seasons', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Create', 'url' => route('admin.seasons.create')],
        ];

        return view('admin.seasons.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $name = Str::upper($request->season_name);

        $validator = Validator::make($request->all(), [
            'season_name' => 'string|required|unique:seasons,name',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $season = new Season;
        $season->name = $name;

        if ($season->save()) {
            return redirect(route('admin.seasons.index'))->with('success', 'Season saved successfully!');
        }

        return redirect(route('admin.seasons.index'))->with('error', 'Something went wrong!');
    }

    public function show(Season $season)
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Show', 'url' => route('admin.seasons.show', $season->id)],
        ];

        return view('admin.seasons.show', compact('season', 'breadcrumb'));
    }

    public function edit(Season $season)
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Edit', 'url' => route('admin.seasons.edit', $season->id)],
        ];

        return view('admin.seasons.edit', compact('season', 'breadcrumb'));
    }

    public function update(Request $request, Season $season)
    {

        $name = Str::upper($request->season_name);

        $validator = Validator::make($request->all(), [
            'season_name' => 'string|required|unique:seasons,name,'.$season->id,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $season->update([
            'name' => $name,
        ]);

        return redirect(route('admin.seasons.index'))->with('success', 'Season updated successfully!');
    }

    public function destroy(Season $season)
    {
        if ($season->delete()) {
            return redirect(route('admin.seasons.index'))->with('success', 'Season deleted successfully!');
        } else {
            return redirect(route('admin.seasons.index'))->with('danger', 'An error has been ocurred!');
        }
    }

    public function setCurrent(Season $season)
    {
        $season->setCurrent();

        return redirect(route('admin.seasons.index'))->with('success', 'Season updated successfully!');
    }
}
