<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommentReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentReportController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required|integer|exists:comments,id',
                'title' => 'required|max:255|string',
                'content' => 'string|nullable',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                $report = new CommentReport();
                $report->comment_id = $request->comment_id;
                $report->user_id = Auth::id();
                $report->title = $request->title;
                $report->content = $request->content ?? 'No details provided';
                $report->source = $request->header('Referer') ?? url()->previous();
                $report->status = CommentReport::STATUS_PENDING;
                $report->save();

                return redirect()->back()->with('success', 'Thanks for your report! Our staff will review it soon.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to submit report: ' . $th->getMessage());
            }
        }

        return redirect()->back()->with('warning', 'Please login to send a report');
    }
}
