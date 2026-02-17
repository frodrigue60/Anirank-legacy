@extends('layouts.admin')

@section('title', 'Roles Directory')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Role Administration</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Access Control & Permissions
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">add_moderator</span>
                    CREATE ROLE
                </a>
            </div>
        </div>


        <div class="space-y-6">
            {{-- Search Bar --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-6">
                <form action="{{ route('admin.roles.index') }}" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-outlined text-zinc-500 group-focus-within:text-blue-500 transition-colors">search</span>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="block w-full pl-11 pr-32 py-4 bg-zinc-950/50 border border-zinc-800 text-white placeholder-zinc-500 rounded-2xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                        placeholder="Search roles by name or slug...">
                    <div class="absolute inset-y-2 right-2 flex items-center">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg active:scale-95">
                            SEARCH
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/50 border-b border-zinc-800">
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">ID</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Role Name
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Slug</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Users
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @foreach ($roles as $role)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-mono text-zinc-500">#{{ $role->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">
                                                    {{ $role->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border bg-zinc-800 text-zinc-400 border-zinc-700">
                                            {{ $role->slug }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-zinc-400 max-w-xs truncate">
                                        <a href="{{ route('admin.users.index', ['role_id' => $role->id]) }}"
                                            class="text-blue-400 transition-colors px-2 py-1 rounded-full bg-zinc-800 hover:bg-blue-600 hover:text-white">
                                            {{ $role->users()->count() }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500"
                                                title="Edit Role">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="post"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete role?')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500"
                                                    title="Delete Role">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($roles->hasPages())
                    <div class="bg-zinc-950/50 px-6 py-8 border-t border-zinc-800">
                        <div class="flex justify-center">
                            {{ $roles->links('vendor.pagination.tailwind') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
