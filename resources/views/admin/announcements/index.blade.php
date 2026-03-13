@extends('layouts.admin')

@section('title', 'Manage Announcements')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Announcements</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Sidebar Notifications & News</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.announcements.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">add_circle</span>
                    ADD ANNOUNCEMENT
                </a>
            </div>
        </div>


        <div class="space-y-6">
            {{-- Search Bar --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-6">
                <form action="{{ route('admin.announcements.index') }}" method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-outlined text-zinc-500 group-focus-within:text-blue-500 transition-colors">search</span>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="block w-full pl-11 pr-32 py-4 bg-zinc-950/50 border border-zinc-800 text-white placeholder-zinc-500 rounded-2xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                        placeholder="Search announcements...">
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
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Title</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Type</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Priority</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @foreach ($announcements as $announcement)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-mono text-zinc-500">#{{ $announcement->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($announcement->icon)
                                                <span class="material-symbols-outlined text-primary">{{ $announcement->icon }}</span>
                                            @endif
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-white">{{ $announcement->title }}</span>
                                                @if($announcement->starts_at)
                                                    <span class="text-[10px] text-zinc-500">{{ $announcement->starts_at->format('M d, Y') }} - {{ $announcement->ends_at ? $announcement->ends_at->format('M d, Y') : 'Life' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            @if($announcement->type == 'info') bg-blue-500/10 text-blue-500 border border-blue-500/20
                                            @elseif($announcement->type == 'success') bg-green-500/10 text-green-500 border border-green-500/20
                                            @elseif($announcement->type == 'warning') bg-amber-500/10 text-amber-500 border border-amber-500/20
                                            @elseif($announcement->type == 'danger') bg-red-500/10 text-red-500 border border-red-500/20
                                            @else bg-purple-500/10 text-purple-400 border border-purple-500/20 @endif">
                                            {{ $announcement->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($announcement->is_active)
                                            <span class="flex items-center justify-center text-green-500" title="Active">
                                                <span class="material-symbols-outlined filled text-lg">check_circle</span>
                                            </span>
                                        @else
                                            <span class="flex items-center justify-center text-zinc-600" title="Inactive">
                                                <span class="material-symbols-outlined filled text-lg">cancel</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-mono text-zinc-400">{{ $announcement->priority }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.announcements.edit', $announcement->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500"
                                                title="Edit Announcement">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <form action="{{ route('admin.announcements.destroy', $announcement->id) }}"
                                                method="post" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete announcement?')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500"
                                                    title="Delete Announcement">
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
                <div class="bg-zinc-950/50 px-6 py-8 border-t border-zinc-800">
                    <div class="flex justify-center">
                        {{ $announcements->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
