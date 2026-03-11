<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentReportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|exists:comments,id',
            'title' => 'required|max:255|string',
            'content' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $source = $request->header('Referer') ?? 'api';
            
            $report = CommentReport::create([
                'comment_id' => $request->comment_id,
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $request->content ?? 'No details provided',
                'source' => substr($source, 0, 255),
                'status' => CommentReport::STATUS_PENDING,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data' => $report
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
