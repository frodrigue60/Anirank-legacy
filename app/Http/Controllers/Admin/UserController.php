<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
        ];

        $users = User::with('roles');
        $selectedRole = null;

        if ($request->has('q')) {
            $users->where('name', 'LIKE', '%'.$request->q.'%')
                ->orWhere('email', 'LIKE', '%'.$request->q.'%');
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

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Create', 'url' => route('admin.users.create')],
        ];
        $roles = Role::all();

        return view('admin.users.create', compact('roles', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_src' => ['nullable', 'url', 'max:255'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'banner_src' => ['nullable', 'url', 'max:255'],
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

            // Handle Avatar & Banner
            if ($request->hasFile('avatar')) {
                $user->avatar = $request->file('avatar')->store('profile', config('filesystems.default'));
            } elseif ($request->filled('avatar_src')) {
                $user->avatar = $request->avatar_src;
            }

            if ($request->hasFile('banner')) {
                $user->banner = $request->file('banner')->store('banners', config('filesystems.default'));
            } elseif ($request->filled('banner_src')) {
                $user->banner = $request->banner_src;
            }

            $user->save();

            // Automate avatar generation for manually created users ONLY if none was provided
            if (!$user->avatar) {
                try {
                    $name = urlencode($user->name);
                    $url = "https://ui-avatars.com/api/?name={$name}&color=fff&background=random&size=512";
                    $response = Http::timeout(5)->get($url);
                    if ($response->successful()) {
                        $file_name = $user->slug . '-avatar-' . time() . '.png';
                        $path = 'profile/' . $file_name;
                        Storage::disk(config('filesystems.default'))->put($path, $response->body());
                        $user->avatar = $path;
                        $user->save();
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Admin could not fetch avatar for user {$user->id}: " . $e->getMessage());
                }
            }

            return Redirect::route('admin.users.index')->with('success', 'User Created Successfully');
        }
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        $breadcrumb = [
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Edit', 'url' => route('admin.users.edit', $user->id)],
        ];
        $user = User::with(['roles', 'badges'])->findOrFail($user->id);
        $roles = Role::all();
        $badges = Badge::all();

        return view('admin.users.edit', compact('user', 'roles', 'badges', 'breadcrumb'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_src' => ['nullable', 'url', 'max:255'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'banner_src' => ['nullable', 'url', 'max:255'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        } else {
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle Avatar Update
            if ($request->hasFile('avatar')) {
                if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                    Storage::disk(config('filesystems.default'))->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('profile', config('filesystems.default'));
            } elseif ($request->filled('avatar_src')) {
                if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                    Storage::disk(config('filesystems.default'))->delete($user->avatar);
                }
                $user->avatar = $request->avatar_src;
            }

            // Handle Banner Update
            if ($request->hasFile('banner')) {
                if ($user->banner && !filter_var($user->banner, FILTER_VALIDATE_URL)) {
                    Storage::disk(config('filesystems.default'))->delete($user->banner);
                }
                $user->banner = $request->file('banner')->store('banners', config('filesystems.default'));
            } elseif ($request->filled('banner_src')) {
                if ($user->banner && !filter_var($user->banner, FILTER_VALIDATE_URL)) {
                    Storage::disk(config('filesystems.default'))->delete($user->banner);
                }
                $user->banner = $request->banner_src;
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

    public function destroy(User $user)
    {
        $user->ratings()->delete();

        if ($user->delete()) {
            return Redirect::route('admin.users.index')->with('success', 'User deleted successfully');
        } else {
            return Redirect::route('admin.users.index')->with('warning', 'Something went wrong!');
        }
    }
}
