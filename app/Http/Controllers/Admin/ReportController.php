<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.reports.index')],
        ];

        $reports = Report::with('song')->latest()->paginate(20);

        return view('admin.reports.index', compact('reports', 'breadcrumb'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Report $report)
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.reports.index')],
            ['name' => 'Show', 'url' => route('admin.reports.show', $report->id)],
        ];

        return view('admin.reports.show', compact('report', 'breadcrumb'));
    }

    public function edit(Report $report)
    {
        //
    }

    public function update(Request $request, Report $report)
    {
        //
    }

    public function destroy(Report $report)
    {
        if ($report->delete()) {
            return redirect(route('admin.reports.index'))->with('success', 'Report '.$report->id.' deleted');
        }

        return redirect(route('admin.reports.index'))->with('error', 'Report '.$report->id.' has not been deleted!');
    }

    public function toggle(Report $report)
    {
        try {
            $report->toggle();

            return redirect()->back()->with('success', 'Report #'.$report->id.' status updated to '.$report->status);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update report: '.$th->getMessage());
        }
    }
}
