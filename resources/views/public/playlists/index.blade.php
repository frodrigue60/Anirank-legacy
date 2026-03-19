@extends('layouts.app')

@section('meta')
    <title>{{ request()->has('owned') ? 'My Playlists' : 'Explore Playlists' }} | {{ config('app.name') }}</title>
    <meta name="description" content="Explore custom anime music playlists from the Anirank community.">
@endsection

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-10 md:py-8 flex flex-col gap-12">
        <div class="flex flex-col gap-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white mb-2 flex items-center gap-4">
                        <span class="material-symbols-outlined text-primary text-4xl">featured_play_list</span>
                        {{ request()->has('owned') ? 'My Playlists' : 'Explore Playlists' }}
                    </h1>
                    <div class="h-1 w-20 bg-primary rounded-full"></div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    {{-- Filter Tabs --}}
                    @auth
                        <div class="flex bg-surface-darker/50 p-1 rounded-xl border border-white/5">
                            <a href="{{ route('playlists.index') }}" 
                               class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all {{ !request()->has('owned') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/40 hover:text-white' }}">
                                Explore
                            </a>
                            <a href="{{ route('playlists.index', ['owned' => 1]) }}" 
                               class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all {{ request()->has('owned') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/40 hover:text-white' }}">
                                My Playlists
                            </a>
                        </div>
                    @endauth

                    <a href="{{ route('playlists.create') }}"
                        class="bg-white/5 hover:bg-white/10 text-white text-xs font-black uppercase px-6 py-3 rounded-xl border border-white/10 transition-all flex items-center gap-2 group">
                        <span
                            class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform">add</span>
                        Create Playlist
                    </a>
                </div>
            </div>

            {{-- Grid Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($playlists as $playlist)
                    @php
                        $cover = asset('resources/images/song_cover.png');
                        $firstSongWithPost = $playlist->songs->first(fn($s) => $s->anime);
                        if ($firstSongWithPost) {
                            $cover = $firstSongWithPost->anime->cover_url;
                        }
                    @endphp

                    <div
                        class="group relative bg-surface-dark/30 rounded-2xl border border-white/5 p-5 backdrop-blur-sm hover:bg-surface-dark/50 transition-all flex flex-col gap-4 overflow-hidden aspect-video">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105 opacity-60"
                            style="background-image: url('{{ $cover }}')">
                        </div>
                        <div class="absolute inset-0 bg-linear-to-t from-black via-black/60 to-transparent"></div>
                        
                        {{-- Owner Actions --}}
                        @auth
                            @if($playlist->user_id === Auth::id())
                                <div class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2">
                                    <a href="{{ route('playlists.edit', $playlist->id) }}"
                                        class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-primary text-white rounded-lg backdrop-blur-md transition-colors border border-white/10">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST" onsubmit="return confirm('Delete this playlist?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-red-500 text-white rounded-lg backdrop-blur-md transition-colors border border-white/10">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <div class="absolute bottom-0 left-0 right-0 p-5 flex flex-col gap-2">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-slate-400 text-[10px] font-bold flex items-center gap-1 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-xs">music_note</span>
                                    {{ $playlist->songs_count }} Themes
                                </span>
                                @if(!$playlist->is_public)
                                    <span class="text-amber-500 text-[10px] font-bold flex items-center gap-1 uppercase tracking-wider bg-amber-500/10 px-2 py-0.5 rounded">
                                        <span class="material-symbols-outlined text-xs">lock</span>
                                        Private
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('playlists.show', $playlist->id) }}"
                                class="text-xl font-black text-white group-hover:text-primary transition-colors uppercase leading-tight">
                                {{ $playlist->name }}</a>
                            <p class="text-white/40 text-[11px] font-bold uppercase tracking-widest leading-none">
                                Curated by <span class="text-white hover:text-primary transition-colors cursor-pointer">{{ $playlist->user->name }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 flex flex-col items-center justify-center text-center opacity-40">
                        <span class="material-symbols-outlined text-7xl mb-4">playlist_add</span>
                        <p class="text-xl font-bold">No playlists found</p>
                        <p class="text-sm font-medium mt-2">Try adjusting your filters or create your own playlist!</p>
                        <a href="{{ route('playlists.create') }}" class="mt-6 text-primary font-bold hover:underline">Create
                            Playlist</a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $playlists->links() }}
            </div>
        </div>
    </div>
@endsection


