<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Requests', 'url' => route('admin.requests.index')],
        ];
        $requests = UserRequest::all();
        $requests = $this->paginate($requests, $perPage = 10);
        return view('admin.requests.index', compact('requests', 'breadcrumb'));
    }

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $breadcrumb = [
            ['name' => 'Requests', 'url' => route('admin.requests.index')],
            ['name' => 'Show', 'url' => route('admin.requests.show', $id)],
        ];
        $userRequest = UserRequest::with('user')->findOrFail($id);

        return view('admin.requests.show', compact('userRequest', 'breadcrumb'));
    }

    /**
     * Mark the specified resource as attended.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attend($id)
    {
        $userRequest = UserRequest::findOrFail($id);
        $userRequest->attended_by = Auth::id();
        $userRequest->status = 'attended';
        $userRequest->save();

        return Redirect::back()->with('success', 'Request marked as attended.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userRequest = UserRequest::find($id);
        //dd($userRequest);
        $userRequest->delete();
        return redirect(route('admin.requests.index'))->with('success', 'Request deleted successfully');
    }
    public function paginate($posts, $perPage, $page = null, $options = [])
    {
        $page = Paginator::resolveCurrentPage();
        $options = ['path' => Paginator::resolveCurrentPath()];
        $items = $posts instanceof Collection ? $posts : Collection::make($posts);
        $posts = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $posts;
    }
}
