<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Badge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
        ];

        $users = User::with('roles');
        $selectedRole = null;

        if ($request->has('q')) {
            $users->where('name', 'LIKE', '%' . $request->q . '%')
                ->orWhere('email', 'LIKE', '%' . $request->q . '%');
        }

        if ($request->has('role_id')) {
            $selectedRole = Role::find($request->role_id);
            if ($selectedRole) {
                $users->whereHas('roles', function ($query) use ($selectedRole) {
                    $query->where('roles.id', $selectedRole->id);
                });
                $breadcrumb[] = ['name' => $selectedRole->name, 'url' => '#'];
            }
        }

        $users = $users->paginate(10);
        return view('admin.users.index', compact('users', 'breadcrumb', 'selectedRole'));
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
        $roles = Role::all();
        return view('admin.users.create', compact('roles', 'breadcrumb'));
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
            return Redirect::back()->withErrors($validator)->withInput();
        } else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            if ($request->has('role_id')) {
                $user->roles()->sync($request->role_id);
            } else {
                // Default to 'user' role if none provided
                $userRole = Role::where('slug', 'user')->first();
                if ($userRole) {
                    $user->roles()->attach($userRole->id);
                }
            }

            // Automate avatar generation for manually created users
            try {
                $name = urlencode($user->name);
                $url = "https://ui-avatars.com/api/?name={$name}&color=fff&background=random&size=512";
                $response = Http::timeout(5)->get($url);
                if ($response->successful()) {
                    $file_name = $user->slug . '-avatar-' . time() . '.png';
                    $path = 'profile/' . $file_name;
                    Storage::disk('public')->put($path, $response->body());
                    $user->updateOrCreateImage($path, 'avatar');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Admin could not fetch avatar for user {$user->id}: " . $e->getMessage());
            }

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
        $user = User::with(['roles', 'badges'])->findOrFail($id);
        $roles = Role::all();
        $badges = Badge::all();
        return view('admin.users.edit', compact('user', 'roles', 'badges', 'breadcrumb'));
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
            return Redirect::back()->withErrors($validator)->withInput();
        } else {
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                if ($request->has('role_id')) {
                    $user->roles()->sync($request->role_id);
                }

                if ($request->has('badge_id')) {
                    $user->badges()->sync($request->badge_id);
                } else {
                    $user->badges()->detach();
                }

                return Redirect::route('admin.users.index')->with('success', 'User Updated Successfully');
            } else {
                return Redirect::back()->with('error', 'Something went wrong!');
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

    public function paginate($posts, $perPage = null, $page = null, $options = [])
    {
        $page = Paginator::resolveCurrentPage();
        $options = ['path' => Paginator::resolveCurrentPath()];
        $items = $posts instanceof Collection ? $posts : Collection::make($posts);
        $posts = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $posts;
    }
}
