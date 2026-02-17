<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
        ];

        $roles = Role::query();

        if ($request->has('q')) {
            $roles->where('name', 'LIKE', '%' . $request->q . '%')
                ->orWhere('slug', 'LIKE', '%' . $request->q . '%');
        }

        $roles = $roles->paginate(10);

        return view('admin.roles.index', compact('roles', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
            ['name' => 'Create', 'url' => route('admin.roles.create')],
        ];

        return view('admin.roles.create', compact('breadcrumb'));
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
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'nullable|string|max:255|unique:roles',
            'description' => 'nullable|string',
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
        ]);

        return Redirect::route('admin.roles.index')->with('success', 'Role Created Successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
            ['name' => 'Edit', 'url' => route('admin.roles.edit', $id)],
        ];

        return view('admin.roles.edit', compact('role', 'breadcrumb'));
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
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
        ]);

        return Redirect::route('admin.roles.index')->with('success', 'Role Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of critical roles if necessary, e.g., admin
        if ($role->slug === 'admin') {
            return Redirect::route('admin.roles.index')->with('error', 'The Admin role cannot be deleted.');
        }

        if ($role->delete()) {
            return Redirect::route('admin.roles.index')->with('success', 'Role deleted successfully');
        } else {
            return Redirect::route('admin.roles.index')->with('warning', 'Something went wrong!');
        }
    }
}
