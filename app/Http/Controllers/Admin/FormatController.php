<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Format;
use Illuminate\Http\Request;

class FormatController extends Controller
{
    public function index()
    {
        $formats = Format::orderBy('name')->paginate(20);
        return view('admin.formats.index', compact('formats'));
    }

    public function create()
    {
        return view('admin.formats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:formats,name',
            'slug' => 'required|string|max:255|unique:formats,slug',
        ]);

        Format::create($validated);

        return redirect()->route('admin.formats.index')
            ->with('success', 'Format created successfully.');
    }

    public function show(Format $format)
    {
        return view('admin.formats.show', compact('format'));
    }

    public function edit(Format $format)
    {
        return view('admin.formats.edit', compact('format'));
    }

    public function update(Request $request, Format $format)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:formats,name,' . $format->id,
            'slug' => 'required|string|max:255|unique:formats,slug,' . $format->id,
        ]);

        $format->update($validated);

        return redirect()->route('admin.formats.index')
            ->with('success', 'Format updated successfully.');
    }

    public function destroy(Format $format)
    {
        $format->delete();

        return redirect()->route('admin.formats.index')
            ->with('success', 'Format deleted successfully.');
    }
}
