<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentReportController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Comment Reports', 'url' => route('admin.comment-reports.index')],
        ];
        
        $reports = CommentReport::with(['user', 'comment'])->latest()->paginate(20);
        
        return view('admin.comment-reports.index', compact('reports', 'breadcrumb'));
    }

    public function show(CommentReport $report)
    {
        $breadcrumb = [
            ['name' => 'Comment Reports', 'url' => route('admin.comment-reports.index')],
            ['name' => 'Show', 'url' => route('admin.comment-reports.show', $report->id)],
        ];
        
        $report->load(['user', 'comment.user', 'comment.song']);
        
        return view('admin.comment-reports.show', compact('report', 'breadcrumb'));
    }

    public function toggle(CommentReport $report)
    {
        $report->toggle();

        return redirect()->route('admin.comment-reports.index')->with('success', 'Report status updated.');
    }

    public function destroy(CommentReport $report)
    {
        $report->delete();

        return redirect()->route('admin.comment-reports.index')->with('success', 'Report deleted.');
    }
}
