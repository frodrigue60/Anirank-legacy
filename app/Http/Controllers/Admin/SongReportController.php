<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongReport;
use Illuminate\Http\Request;

class SongReportController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.song-reports.index')],
        ];

        $reports = SongReport::with('song')->latest()->paginate(20);

        return view('admin.song-reports.index', compact('reports', 'breadcrumb'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(SongReport $report)
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.song-reports.index')],
            ['name' => 'Show', 'url' => route('admin.song-reports.show', $report->id)],
        ];

        return view('admin.song-reports.show', compact('report', 'breadcrumb'));
    }

    public function edit(SongReport $report)
    {
        //
    }

    public function update(Request $request, SongReport $report)
    {
        //
    }

    public function destroy(SongReport $report)
    {
        if ($report->delete()) {
            return redirect(route('admin.song-reports.index'))->with('success', 'Report '.$report->id.' deleted');
        }

        return redirect(route('admin.song-reports.index'))->with('error', 'Report '.$report->id.' has not been deleted!');
    }

    public function toggle(SongReport $report)
    {
        try {
            $report->toggle();

            return redirect()->back()->with('success', 'Report #'.$report->id.' status updated to '.$report->status);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update report: '.$th->getMessage());
        }
    }
}
