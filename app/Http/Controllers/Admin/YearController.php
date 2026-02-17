<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
        ];
        $years = Year::all();
        return view('admin.years.index', compact('years', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Create', 'url' => route('admin.years.create')],
        ];
        return view('admin.years.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Show', 'url' => route('admin.years.show', $id)],
        ];
        $year = Year::find($id);
        return view('admin.years.show', compact('year', 'breadcrumb'));
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
            ['name' => 'Years', 'url' => route('admin.years.index')],
            ['name' => 'Edit', 'url' => route('admin.years.edit', $id)],
        ];
        $year = Year::find($id);
        return view('admin.years.edit', compact('year', 'breadcrumb'));
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
        $validator = Validator::make($request->all(), [
            'year' => 'integer|required|unique:years,name|digits:4',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $year = Year::find($id);
        $year->update(['name' => $request->year]);

        return redirect(route('admin.years.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $year = Year::find($id);
        $year->delete();
        return redirect(route('admin.years.index'));
    }

    public function setCurrent(Year $year)
    {
        $year->setCurrent();

        return redirect(route('admin.years.index'))->with('success', 'Year updated successfully!');
    }
}
