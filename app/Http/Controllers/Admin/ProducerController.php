<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProducerController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Producers', 'url' => route('admin.producers.index')],
        ];
        $producers = Producer::paginate(15);

        return view('admin.producers.index', compact('producers', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Producers', 'url' => route('admin.producers.index')],
            ['name' => 'Create', 'url' => route('admin.producers.create')],
        ];

        return view('admin.producers.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:producers,name',
            'logo' => 'nullable|image|max:2048',
            'logo_src' => 'nullable|url|max:255'
        ]);

        $producer = Producer::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('logo')) {
            $producer->logo = $request->file('logo')->store('producers', config('filesystems.default'));
            $producer->save();
        } elseif ($request->filled('logo_src')) {
            $producer->logo = $request->logo_src;
            $producer->save();
        }

        return redirect()->route('admin.producers.index')->with('success', 'Producer created successfully.');
    }

    public function edit(Producer $producer)
    {
        $breadcrumb = [
            ['name' => 'Producers', 'url' => route('admin.producers.index')],
            ['name' => 'Edit', 'url' => route('admin.producers.edit', $producer->id)],
        ];

        return view('admin.producers.edit', compact('producer', 'breadcrumb'));
    }

    public function update(Request $request, Producer $producer)
    {
        $request->validate([
            'name' => 'required|unique:producers,name,'.$producer->id,
            'logo' => 'nullable|image|max:2048',
            'logo_src' => 'nullable|url|max:255'
        ]);

        $producer->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        if ($request->hasFile('logo')) {
            if ($producer->logo && !\Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->exists($producer->logo)) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($producer->logo);
            }
            $producer->logo = $request->file('logo')->store('producers', config('filesystems.default'));
            $producer->save();
        } elseif ($request->filled('logo_src')) {
            if ($producer->logo && !filter_var($producer->logo, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($producer->logo);
            }
            $producer->logo = $request->logo_src;
            $producer->save();
        }

        return redirect()->route('admin.producers.index')->with('success', 'Producer updated successfully.');
    }

    public function destroy(Producer $producer)
    {
        if ($producer->delete()) {
            return redirect()->route('admin.producers.index')->with('success', 'Producer deleted successfully.');
        }

        return redirect()->route('admin.producers.index')->with('error', 'Producer has not been deleted!');
    }
}
