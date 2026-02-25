<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
        ];
        $studios = Studio::paginate(15);

        return view('admin.studios.index', compact('studios', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
            ['name' => 'Create', 'url' => route('admin.studios.create')],
        ];

        return view('admin.studios.create', compact('breadcrumb'));
    }

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

    public function edit(Studio $studio)
    {
        $breadcrumb = [
            ['name' => 'Studios', 'url' => route('admin.studios.index')],
            ['name' => 'Edit', 'url' => route('admin.studios.edit', $studio->id)],
        ];

        return view('admin.studios.edit', compact('studio', 'breadcrumb'));
    }

    public function update(Request $request, Studio $studio)
    {
        $request->validate([
            'name' => 'required|unique:studios,name,'.$studio->id,
        ]);

        $studio->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.studios.index')->with('success', 'Studio updated successfully.');
    }

    public function destroy(Studio $studio)
    {
        if ($studio->delete()) {
            return redirect()->route('admin.studios.index')->with('success', 'Studio deleted successfully.');
        } else {
            return redirect()->route('admin.studios.index')->with('error', 'Error deleting studio.');
        }
    }
}
