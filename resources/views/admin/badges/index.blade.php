@extends('layouts.admin')

@section('title', 'Badges Administration')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Badge Management</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Rewards & Achievements System
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.badges.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">add_task</span>
                    CREATE BADGE
                </a>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Search Bar --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-6">
                <form action="{{ route('admin.badges.index') }}" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-outlined text-zinc-500 group-focus-within:text-blue-500 transition-colors">search</span>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="block w-full pl-11 pr-32 py-4 bg-zinc-950/50 border border-zinc-800 text-white placeholder-zinc-500 rounded-2xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                        placeholder="Search badges by name...">
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
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Icon</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Badge Name
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Users</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse ($badges as $badge)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div
                                            class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-950 border border-zinc-800 shadow-inner flex items-center justify-center">
                                            <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">
                                                {{ $badge->name }}
                                            </span>
                                            <span class="text-[10px] text-zinc-500 max-w-[200px] truncate">
                                                {{ $badge->description }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($badge->is_active)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 uppercase tracking-widest">Active</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-red-500/10 text-red-500 border border-red-500/20 uppercase tracking-widest">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-zinc-400 tabular-nums">
                                            {{ $badge->users()->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.badges.edit', $badge->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500"
                                                title="Edit Badge">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="post"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete badge?')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500"
                                                    title="Delete Badge">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-zinc-500 text-sm italic">
                                        No badges found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($badges->hasPages())
                    <div class="bg-zinc-950/50 px-6 py-8 border-t border-zinc-800">
                        <div class="flex justify-center">
                            {{ $badges->links('vendor.pagination.tailwind') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


