<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

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

        $reports = Report::with('song')->latest()->paginate(20);

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
    public function show(report $report)
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.reports.index')],
            ['name' => 'Show', 'url' => route('admin.reports.show', $report->id)],
        ];

        return view('admin.reports.show', compact('report', 'breadcrumb'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(report $report)
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
    public function destroy(report $report)
    {
        $report->delete();
        return redirect(route('admin.reports.index'))->with('warning', 'Report ' . $report->id . ' deleted');
    }

    public function toggle(report $report)
    {
        try {
            $report->toggle();

            return redirect()->back()->with('success', 'Report #' . $report->id . ' status updated to ' . $report->status);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update report: ' . $th->getMessage());
        }
    }
}
