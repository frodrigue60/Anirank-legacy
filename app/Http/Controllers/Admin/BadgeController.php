<?php

namespace App\Http\Controllers\Admin;

use App\Models\Badge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Badges', 'url' => route('admin.badges.index')],
        ];

        $badges = Badge::query();
        if ($request->filled('q')) {
            $badges->where('name', 'like', "%{$request->q}%");
        }

        $badges = $badges->paginate(15);

        return view('admin.badges.index', compact('badges', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Badges', 'url' => route('admin.badges.index')],
            ['name' => 'Create', 'url' => ''],
        ];
        return view('admin.badges.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'icon' => 'nullable|image|max:2048'
        ]);

        $badge = Badge::create($request->only(['name', 'description', 'is_active']));

        if ($request->hasFile('icon')) {
            $extension = $request->file('icon')->extension();
            $file_name = \Illuminate\Support\Str::slug($badge->name) . '-' . time() . '.' . $extension;
            $path = $request->file('icon')->storeAs('badges', $file_name, 'public');
            $badge->updateOrCreateImage($path, 'icon');
        }

        return redirect()->route('admin.badges.index')->with('success', 'Badge created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        $breadcrumb = [
            ['name' => 'Badges', 'url' => route('admin.badges.index')],
            ['name' => 'Edit', 'url' => ''],
        ];
        return view('admin.badges.edit', compact('badge', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'icon' => 'nullable|image|max:2048'
        ]);

        $badge->update($request->only(['name', 'description', 'is_active']));

        if ($request->hasFile('icon')) {
            $extension = $request->file('icon')->extension();
            $file_name = \Illuminate\Support\Str::slug($badge->name) . '-' . time() . '.' . $extension;
            $path = $request->file('icon')->storeAs('badges', $file_name, 'public');
            $badge->updateOrCreateImage($path, 'icon');
        }

        return redirect()->route('admin.badges.index')->with('success', 'Badge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badges.index')->with('success', 'Badge deleted successfully.');
    }
}
