<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
        ];
        $users =  User::all();
        $users = $users->sortByDesc('created_at');
        $users = $this->paginate($users, $perPage = 5);
        return view('admin.users.index', compact('users', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Create', 'url' => route('admin.users.create')],
        ];
        $type = [
            ['name' => 'User', 'value' => 'user'],
            ['name' => 'Admin', 'value' => 'admin'],
            ['name' => 'Editor', 'value' => 'editor'],
            ['name' => 'Creator', 'value' => 'creator']
        ];
        return view('admin.users.create', compact('type', 'breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();
            return Redirect::back()
                ->withInput([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'userType' => $request->input('userType'),
                ])
                ->with('error', $messageBag);
        } else {
            User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            return Redirect::route('admin.users.index')->with('success', 'User Created Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Edit', 'url' => route('admin.users.edit', $id)],
        ];
        $user = User::find($id);
        $type = [
            ['name' => 'User', 'value' => 'user'],
            ['name' => 'Admin', 'value' => 'admin'],
            ['name' => 'Editor', 'value' => 'editor'],
            ['name' => 'Creator', 'value' => 'creator']
        ];
        return view('admin.users.edit', compact('user', 'type', 'breadcrumb'));
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
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            /* 'password' => ['required', 'string', 'min:4'], */
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            return Redirect::back()->with('error', $errors);
        } else {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->type = $request->userType;
            if ($request->password != null) {
                $user->password = Hash::make($request['password']);
            }
            if ($user->update()) {
                return Redirect::route('admin.users.index')->with('success', 'User Updated Successfully');
            } else {
                return Redirect::route('admin.users.index')->with('error', 'Somethis was wrong!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $deleteRatings = DB::table('ratings')->where('user_id', $user->id)->delete();

        if ($user->delete()) {
            return Redirect::route('admin.users.index')->with('success', 'User deleted successfully');
        } else {
            return Redirect::route('admin.users.index')->with('warning', 'Somethis was wrong!');
        }
    }

    public function searchUser(Request $request)
    {
        $users = User::query()
            ->where('name', 'LIKE', '%' . $request->input('q') . '%')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function paginate($posts, $perPage = null, $page = null, $options = [])
    {
        $page = Paginator::resolveCurrentPage();
        $options = ['path' => Paginator::resolveCurrentPath()];
        $items = $posts instanceof Collection ? $posts : Collection::make($posts);
        $posts = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $posts;
    }
}
