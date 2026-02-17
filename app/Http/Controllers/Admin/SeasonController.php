<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
        ];
        $seasons = Season::all();
        return view('admin.seasons.index', compact('seasons', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Create', 'url' => route('admin.seasons.create')],
        ];
        return view('admin.seasons.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        $season = new Season();
        $season->name = $name;

        if ($season->save()) {
            return redirect(route('admin.seasons.index'))->with('success', 'Season saved successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Show', 'url' => route('admin.seasons.show', $id)],
        ];
        $season = Season::find($id);
        return view('admin.seasons.show', compact('season', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumb = [
            ['name' => 'Seasons', 'url' => route('admin.seasons.index')],
            ['name' => 'Edit', 'url' => route('admin.seasons.edit', $id)],
        ];
        $season = Season::find($id);
        return view('admin.seasons.edit', compact('season', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $name = Str::upper($request->season_name);
        $exists = Season::where('name', $name)->exists();

        if ($exists) {
            return redirect(route('admin.seasons.index'))->with('warning', 'Season ' . $name . ' already exists!');
        }

        $season = Season::find($id);

        $season->name = $name;

        if ($season->update()) {
            return redirect(route('admin.seasons.index'))->with('success', 'Season updated successfully!');
        } else {
            return redirect(route('admin.seasons.index'))->with('danger', 'An error has been ocurred!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $season = Season::find($id);

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
