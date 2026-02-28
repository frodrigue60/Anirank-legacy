@extends('layouts.admin')

@section('title', 'Manage Songs')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex justify-between items-center gap-6">
            <div>
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">
                        @if (isset($currentPost))
                            Songs: {{ $currentPost->title }}
                        @else
                            Song Catalog
                        @endif
                    </h1>
                    <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">
                        @if (isset($currentPost))
                            Theme Music Registry for this entry
                        @else
                            Global Theme Management & Registry
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    {{-- No general create for songs yet as they are usually linked to posts --}}
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.songs.create', request('anime_id') ? ['anime_id' => request('anime_id')] : []) }}"
                    class="px-6 py-3 bg-primary hover:bg-primary-hover text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-primary/20 active:scale-95 uppercase tracking-widest">
                    <span class="material-symbols-outlined mr-2 text-base">add</span>
                    Add New Song
                </a>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Search Card --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-6">
                <form action="{{ route('admin.songs.index') }}" method="GET" class="relative group">
                    @if (request('anime_id'))
                        <input type="hidden" name="anime_id" value="{{ request('anime_id') }}">
                    @endif
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span
                            class="material-symbols-outlined text-zinc-500 group-focus-within:text-primary transition-colors">search</span>
                    </div>
                    <input type="text" name="q"
                        class="block w-full pl-11 pr-32 py-4 bg-zinc-950/50 border border-zinc-800 text-white placeholder-zinc-500 rounded-2xl focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm"
                        placeholder="Search by title, slug, or anime..." value="{{ request('q') }}">
                    <div class="absolute inset-y-2 right-2 flex items-center">
                        <button type="submit"
                            class="px-6 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-primary/20 active:scale-95 uppercase tracking-widest">
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
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Song Title
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Anime</th>
                                {{-- <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Artists</th> --}}
                                {{-- <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Type</th> --}}
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse ($songs as $song)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-mono text-zinc-500">#{{ $song->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-white group-hover:text-primary transition-colors">{{ $song->name }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-0.5">{{ $song->slug }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($song->anime)
                                            <a href="{{ route('admin.animes.show', $song->anime->id) }}"
                                                class="text-sm text-zinc-300 hover:text-white transition-colors line-clamp-1">
                                                {{ $song->anime->title }}
                                            </a>
                                        @else
                                            <span class="text-xs text-zinc-600 italic">Unlinked</span>
                                        @endif
                                    </td>
                                    {{-- Artists --}}
                                    {{-- <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($song->artists as $artist)
                                                <span
                                                    class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[10px] font-bold uppercase rounded-md border border-zinc-700">
                                                    {{ $artist->name }}
                                                </span>
                                            @empty
                                                <span
                                                    class="text-[10px] text-zinc-600 uppercase font-black tracking-widest">No
                                                    Artists Recorded</span>
                                            @endforelse
                                        </div>
                                    </td> --}}
                                    {{-- Type and Theme Number --}}
                                    {{-- <td class="px-6 py-4">
                                        <span
                                            class="px-2.5 py-1 {{ $song->type == 'OP' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-purple-500/10 text-purple-400 border-purple-500/20' }} text-[10px] font-black uppercase rounded-md border">
                                            {{ $song->type }} {{ $song->theme_num }}
                                        </span>
                                    </td> --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2 shrink-0">
                                            <a href="{{ route('admin.songs.edit', $song->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <a href="{{ route('admin.variants.index', ['song_id' => $song->id]) }}"
                                                class="p-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700"
                                                title="Manage Variants">
                                                <span class="material-symbols-outlined text-sm">layers</span>
                                            </a>
                                            <form action="{{ route('admin.songs.destroy', $song->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Delete this song? All variants will be removed.')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="material-symbols-outlined text-5xl text-zinc-700 mb-4">music_off</span>
                                            <h3 class="text-white font-bold uppercase tracking-widest text-sm">No Songs
                                                Found</h3>
                                            <p class="text-zinc-500 text-xs mt-2">Try adjusting your search filters or
                                                import new entries from the Anime section.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($songs->hasPages())
                    <div class="bg-zinc-950/50 px-6 py-6 border-t border-zinc-800">
                        <div class="flex flex-col items-center">
                            <div class="text-zinc-500 text-xs mb-4 uppercase font-black tracking-widest">
                                PAGE <span class="text-white">{{ $songs->currentPage() }}</span> OF <span
                                    class="text-white">{{ $songs->lastPage() }}</span>
                            </div>
                            <div class="pagination-custom">
                                {{ $songs->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }

        .pagination-custom nav svg {
            display: inline-block;
            width: 1.25rem;
        }

        .pagination-custom nav div:first-child {
            display: none;
        }

        .pagination-custom nav div:last-child {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>
@endpush
