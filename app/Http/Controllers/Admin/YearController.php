<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YearController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
        ];
        $years = Year::all();

        return view('admin.years.index', compact('years', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Create', 'url' => route('admin.years.create')],
        ];

        return view('admin.years.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'integer|required|unique:years,name|digits:4',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $year = Year::Create([
            'name' => $request->year,
        ]);

        return redirect(route('admin.years.index'));
    }

    public function show(Year $year)
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Show', 'url' => route('admin.years.show', $year->id)],
        ];

        return view('admin.years.show', compact('year', 'breadcrumb'));
    }

    public function edit(Year $year)
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Edit', 'url' => route('admin.years.edit', $year->id)],
        ];

        return view('admin.years.edit', compact('year', 'breadcrumb'));
    }

    public function update(Request $request, Year $year)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'integer|required|unique:years,name|digits:4',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $year->update(['name' => $request->year]);

        return redirect(route('admin.years.index'));
    }

    public function destroy(Year $year)
    {
        $year->delete();

        return redirect(route('admin.years.index'));
    }

    public function setCurrent(Year $year)
    {
        $year->setCurrent();

        return redirect(route('admin.years.index'))->with('success', 'Year updated successfully!');
    }
}
