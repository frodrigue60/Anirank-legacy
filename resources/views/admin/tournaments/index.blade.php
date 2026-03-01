@extends('layouts.admin')

@section('title', 'Tournaments')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Tournaments</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Manage Song Brackets</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.tournaments.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">add</span>
                    CREATE TOURNAMENT
                </a>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">ID
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Size
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Type
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @foreach ($tournaments as $tournament)
                            <tr class="hover:bg-zinc-800/30 transition-colors group">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500 text-center">#{{ $tournament->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-white tracking-wider group-hover:text-blue-400 transition-colors">
                                        {{ $tournament->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm text-zinc-400 font-mono">{{ $tournament->size }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full bg-zinc-800 text-zinc-400">
                                        {{ $tournament->type_filter ?: 'Any' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full 
                                        @if ($tournament->status === 'active') bg-green-500/20 text-green-400 border border-green-500/30
                                        @elseif($tournament->status === 'completed') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                        @else bg-zinc-700/50 text-zinc-400 border border-zinc-600 @endif
                                    ">
                                        {{ $tournament->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($tournament->status === 'draft')
                                            <form action="{{ route('admin.tournaments.seed', $tournament->id) }}"
                                                method="post" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 bg-zinc-800 hover:bg-green-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-green-500"
                                                    title="Seed & Start">
                                                    <span class="material-symbols-outlined text-sm">play_arrow</span>
                                                </button>
                                            </form>
                                        @elseif($tournament->status === 'active')
                                            <form action="{{ route('admin.tournaments.force.round', $tournament->id) }}"
                                                method="post" class="inline">
                                                @csrf
                                                @if ($tournament->current_round == 2)
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to finalize the tournament?')"
                                                        class="p-2 bg-zinc-800 hover:bg-green-600 text-green-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-green-500"
                                                        title="Finalize Tournament">
                                                        <span class="material-symbols-outlined text-sm">done_all</span>
                                                    </button>
                                                @else
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to skip to the next phase?')"
                                                        class="p-2 bg-zinc-800 hover:bg-amber-600 text-amber-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-amber-500"
                                                        title="Skip to Next Phase">
                                                        <span class="material-symbols-outlined text-sm">skip_next</span>
                                                    </button>
                                                @endif
                                            </form>
                                            <a href="{{ route('admin.tournaments.show', $tournament->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-indigo-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-indigo-500"
                                                title="View Bracket">
                                                <span class="material-symbols-outlined text-sm">account_tree</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.tournaments.show', $tournament->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-indigo-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-indigo-500"
                                                title="View Bracket">
                                                <span class="material-symbols-outlined text-sm">account_tree</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.tournaments.edit', $tournament->id) }}"
                                            class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </a>
                                        <form action="{{ route('admin.tournaments.destroy', $tournament->id) }}"
                                            method="post" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this tournament?')"
                                                class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500">
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

            @if ($tournaments->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $tournaments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
