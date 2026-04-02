<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'reported_user_id' => 'required|exists:users,id',
                'reason'           => 'required|string|max:100',
                'content'          => 'required|string',
                'source'           => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Prevent users from reporting themselves
            if (Auth::id() == $request->reported_user_id) {
                return redirect()->back()->with('error', 'You cannot report yourself.');
            }

            try {
                UserReport::create([
                    'reported_user_id' => $request->reported_user_id,
                    'reporter_user_id' => Auth::id(),
                    'reason'           => $request->reason,
                    'content'          => $request->content,
                    'source'           => $request->source ?? 'web',
                    'status'           => false, // Pending
                ]);

                return redirect()->back()->with('success', 'Thanks for your report. Our staff will review it soon.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to submit report: ' . $th->getMessage());
            }
        }

        return redirect()->back()->with('warning', 'Please login to submit a report.');
    }
}
