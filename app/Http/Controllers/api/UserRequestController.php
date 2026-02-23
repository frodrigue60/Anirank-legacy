<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'   => 'required|string|max:255',
                'content' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $userRequest = new UserRequest();
            $userRequest->title = $request->title;
            $userRequest->content = $request->content;
            $userRequest->user_id = Auth::id();
            $userRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Request sent successfully!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $th->getMessage()
            ], 500);
        }
    }
}
