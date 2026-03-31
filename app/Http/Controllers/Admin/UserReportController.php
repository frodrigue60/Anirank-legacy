<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.user-reports.index')],
        ];

        $reports = UserReport::with(['reportedUser', 'reporterUser'])->latest()->paginate(20);

        return view('admin.user-reports.index', compact('reports', 'breadcrumb'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserReport  $report
     * @return \Illuminate\View\View
     */
    public function show(UserReport $report)
    {
        $breadcrumb = [
            ['name' => 'Reports', 'url' => route('admin.user-reports.index')],
            ['name' => 'Show', 'url' => route('admin.user-reports.show', $report->id)],
        ];

        $report->load(['reportedUser', 'reporterUser']);

        return view('admin.user-reports.show', compact('report', 'breadcrumb'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserReport  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(UserReport $report)
    {
        if ($report->delete()) {
            return redirect(route('admin.user-reports.index'))->with('success', 'Report #' . $report->id . ' deleted.');
        }

        return redirect(route('admin.user-reports.index'))->with('error', 'Report #' . $report->id . ' has not been deleted!');
    }

    /**
     * Toggle the status of the report (Resolved/Pending).
     *
     * @param  \App\Models\UserReport  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(UserReport $report)
    {
        try {
            $report->toggle();

            return redirect()->back()->with('success', 'Report #' . $report->id . ' status updated to ' . ($report->status ? 'Resolved' : 'Pending'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update report: ' . $th->getMessage());
        }
    }
}
