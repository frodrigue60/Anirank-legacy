<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserRequestController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Requests', 'url' => route('admin.requests.index')],
        ];
        $requests = UserRequest::latest()->paginate(10);

        return view('admin.requests.index', compact('requests', 'breadcrumb'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(UserRequest $userRequest)
    {
        $breadcrumb = [
            ['name' => 'Requests', 'url' => route('admin.requests.index')],
            ['name' => 'Show', 'url' => route('admin.requests.show', $userRequest->id)],
        ];

        return view('admin.requests.show', compact('userRequest', 'breadcrumb'));
    }

    public function attend(UserRequest $userRequest)
    {
        $userRequest->attended_by = Auth::id();
        $userRequest->status = true;
        $userRequest->save();

        // Notify the user who made the request
        $userRequest->user->notifications()->create([
            'type' => 'activity',
            'subject_id' => $userRequest->id,
            'subject_type' => 'user_request',
            'data' => [
                'request_title' => $userRequest->title,
                'message' => 'Your request "' . $userRequest->title . '" has been attended by staff.',
            ],
        ]);

        return Redirect::back()->with('success', 'Request marked as attended.');
    }

    public function edit(UserRequest $userRequest)
    {
        //
    }

    public function update(Request $request, UserRequest $userRequest)
    {
        //
    }

    public function destroy(UserRequest $userRequest)
    {
        $userRequest->delete();

        return redirect(route('admin.requests.index'))->with('success', 'Request deleted successfully');
    }
}
