<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserReportResource;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserReportController extends Controller
{
    /**
     * Store a newly created user report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'reported_user_id' => 'required|integer|exists:users,id',
            'reason'           => 'required|string|max:100',
            'content'          => 'required|string',
            'source'           => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Prevent self-reporting
        if ($user->id == $request->reported_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot report yourself.',
            ], 403);
        }

        try {
            $report = UserReport::create([
                'reported_user_id' => $request->reported_user_id,
                'reporter_user_id' => $user->id,
                'reason'           => $request->reason,
                'content'          => $request->content,
                'source'           => $request->source ?? 'api',
                'status'           => UserReport::STATUS_PENDING,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data'    => new UserReportResource($report),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report: ' . $th->getMessage(),
            ], 500);
        }
    }
}
