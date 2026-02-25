<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
        ];

        $roles = Role::query();

        if ($request->has('q')) {
            $roles->where('name', 'LIKE', '%'.$request->q.'%')
                ->orWhere('slug', 'LIKE', '%'.$request->q.'%');
        }

        $roles = $roles->paginate(10);

        return view('admin.roles.index', compact('roles', 'breadcrumb'));
    }

    public function create()
    {
        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
            ['name' => 'Create', 'url' => route('admin.roles.create')],
        ];

        return view('admin.roles.create', compact('breadcrumb'));
    }

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

    public function edit(Role $role)
    {
        $breadcrumb = [
            ['name' => 'Roles', 'url' => route('admin.roles.index')],
            ['name' => 'Edit', 'url' => route('admin.roles.edit', $role->id)],
        ];

        return view('admin.roles.edit', compact('role', 'breadcrumb'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
        ]);

        return Redirect::route('admin.roles.index')->with('success', 'Role Updated Successfully');
    }

    public function destroy(Role $role)
    {
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
