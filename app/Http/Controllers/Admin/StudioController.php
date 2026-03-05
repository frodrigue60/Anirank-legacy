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
            'logo' => 'nullable|image|max:2048',
            'logo_src' => 'nullable|url|max:255'
        ]);

        $studio = Studio::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('logo')) {
            $studio->logo = $request->file('logo')->store('studios', config('filesystems.default'));
            $studio->save();
        } elseif ($request->filled('logo_src')) {
            $studio->logo = $request->logo_src;
            $studio->save();
        }

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
            'logo' => 'nullable|image|max:2048',
            'logo_src' => 'nullable|url|max:255'
        ]);

        $studio->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('logo')) {
            if ($studio->logo && !\Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->exists($studio->logo)) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($studio->logo);
            }
            $studio->logo = $request->file('logo')->store('studios', config('filesystems.default'));
            $studio->save();
        } elseif ($request->filled('logo_src')) {
            if ($studio->logo && !filter_var($studio->logo, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($studio->logo);
            }
            $studio->logo = $request->logo_src;
            $studio->save();
        }

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
