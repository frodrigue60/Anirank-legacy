<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
        ];
        $studios = Studio::paginate(15);
        return view('admin.studios.index', compact('studios', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
            ['name' => 'Create', 'url' => route('admin.studios.create')],
        ];
        return view('admin.studios.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:studios,name',
        ]);

        Studio::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.studios.index')->with('success', 'Studio created successfully.');
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
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
            ['name' => 'Edit', 'url' => route('admin.studios.edit', $id)],
        ];
        $studio = Studio::findOrFail($id);
        return view('admin.studios.edit', compact('studio', 'breadcrumb'));
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
        $studio = Studio::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:studios,name,' . $studio->id,
        ]);

        $studio->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.studios.index')->with('success', 'Studio updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        $studio->delete();

        return redirect()->route('admin.studios.index')->with('success', 'Studio deleted successfully.');
    }
}
