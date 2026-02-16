<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.reports.index')],
        ];
        $reports = Report::all();
        $reports = $reports->sortByDesc('created_at');
        $reports = $this->paginate($reports);
        return view('admin.reports.index', compact('reports', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            ['name' => 'Reports', 'url' => route('admin.reports.index')],
            ['name' => 'Show', 'url' => route('admin.reports.show', $id)],
        ];
        $report = Report::find($id);
        return view('admin.reports.show', compact('report', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect(route('admin.reports.index'))->with('warning', 'Report ' . $report->id . ' deleted');
    }

    public function toggleStatus($id)
    {
        $report = Report::find($id);

        if ($report->status == 'fixed') {
            $report->status = 'pending';
        } elseif ($report->status = 'pending') {
            $report->status = 'fixed';
        }

        $report->update();
        return Redirect::route('admin.reports.index')->with('warning', 'Report status ' . $report->id . ' changed!');
    }

    public function paginate($reports, $perPage = 10, $page = null, $options = [])
    {
        $page = Paginator::resolveCurrentPage();
        $options = ['path' => Paginator::resolveCurrentPath()];
        $items = $reports instanceof Collection ? $reports : Collection::make($reports);
        $reports = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $reports;
    }
}
