<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Announcements', 'url' => route('admin.announcements.index')],
        ];

        $query = Announcement::query();
        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%")
                  ->orWhere('content', 'like', "%{$request->q}%");
        }

        $announcements = $query->orderBy('priority', 'desc')->paginate(15);

        return view('admin.announcements.index', compact('announcements', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Announcements', 'url' => route('admin.announcements.index')],
            ['name' => 'Create', 'url' => ''],
        ];

        return view('admin.announcements.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string|in:info,success,warning,danger,event',
            'icon' => 'nullable|string|max:50',
            'url' => 'nullable|url|max:255',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:255',
            'priority' => 'required|integer',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $announcement = new Announcement($request->except(['image_file', 'image_url']));
        $announcement->is_active = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            $announcement->image = $request->file('image_file')->store('announcements', config('filesystems.default'));
        } elseif ($request->filled('image_url')) {
            $announcement->image = $request->image_url;
        }

        $announcement->save();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $breadcrumb = [
            ['name' => 'Announcements', 'url' => route('admin.announcements.index')],
            ['name' => 'Edit', 'url' => ''],
        ];

        return view('admin.announcements.edit', compact('announcement', 'breadcrumb'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string|in:info,success,warning,danger,event',
            'icon' => 'nullable|string|max:50',
            'url' => 'nullable|url|max:255',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:255',
            'priority' => 'required|integer',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $announcement->fill($request->except(['image_file', 'image_url']));
        $announcement->is_active = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            if ($announcement->image && !filter_var($announcement->image, FILTER_VALIDATE_URL)) {
                Storage::disk(config('filesystems.default'))->delete($announcement->image);
            }
            $announcement->image = $request->file('image_file')->store('announcements', config('filesystems.default'));
        } elseif ($request->filled('image_url')) {
            if ($announcement->image && !filter_var($announcement->image, FILTER_VALIDATE_URL)) {
                Storage::disk(config('filesystems.default'))->delete($announcement->image);
            }
            $announcement->image = $request->image_url;
        }

        $announcement->save();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image && !filter_var($announcement->image, FILTER_VALIDATE_URL)) {
            Storage::disk(config('filesystems.default'))->delete($announcement->image);
        }
        
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
