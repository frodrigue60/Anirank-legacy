<?php

namespace App\Http\Controllers;

use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserRequestController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string|max:500',
        ]);

        $saved = UserRequest::create([
            'title'   => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        if ($saved) {
            return redirect()->route('home')->with('success', 'Thanks for your request!');
        }

        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
}
