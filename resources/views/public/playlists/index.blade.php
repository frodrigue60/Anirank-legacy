@extends('layouts.app')

@section('meta')
    <title>My Playlists | {{ config('app.name') }}</title>
    <meta title="My Playlists">
    <meta name="description" content="Manage your custom anime music playlists.">
@endsection

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-10 md:py-8 flex flex-col gap-12">
        <div class="flex flex-col gap-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white mb-2 flex items-center gap-4">
                        <span class="material-symbols-outlined text-primary text-4xl">featured_play_list</span>
                        My Playlists
                    </h1>
                    <div class="h-1 w-20 bg-primary rounded-full"></div>
                </div>

                <a href="{{ route('playlists.create') }}"
                    class="bg-primary hover:bg-primary-light text-white text-xs font-black uppercase px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2 group">
                    <span
                        class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform">add</span>
                    Create New Playlist
                </a>
            </div>

            {{-- Grid Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($playlists as $playlist)
                    @php
                        $cover = asset('resources/images/song_cover.png');
                        $firstSong = $playlist->songs->first();
                        if ($firstSong && $firstSong->post) {
                            $cover = $firstSong->anime->cover_url;
                        }
                    @endphp

                    <div
                        class="group relative bg-surface-dark/30 rounded-2xl border border-white/5 p-5 backdrop-blur-sm hover:bg-surface-dark/50 transition-all flex flex-col gap-4 overflow-hidden aspect-video">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105"
                            data-alt="Cyberpunk city neon streets art" style="background-image: url('{{ $cover }}')">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                        <div class="absolute top-0 right-0 p-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('playlists.edit', $playlist->id) }}"
                                    class="text-gray hover:text-primary bg-slate-300 rounded-md p-2">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray hover:text-red-600 bg-slate-300 rounded-md p-2">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 flex flex-col gap-2">
                            <div class="flex items-center gap-2 mb-2">
                                {{--  <span
                                    class="px-2 py-0.5 rounded bg-primary/80 text-white text-[8px] font-bold uppercase">Trending</span>
                                 --}}<span class="text-slate-400 text-[10px] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">music_note</span>
                                    {{ $playlist->songs_count }} Songs
                                </span>
                            </div>
                            <a href="{{ route('playlists.show', $playlist->id) }}"
                                class="text-xl font-bold text-white mb-1 group-hover:text-primary transition-colors uppercase">
                                {{ $playlist->name }}</a>
                            <p class="text-slate-300 text-xs font-medium opacity-80">Curated by <span
                                    class="text-primary font-semibold">{{ $playlist->user->name }}</span></p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 flex flex-col items-center justify-center text-center opacity-40">
                        <span class="material-symbols-outlined text-7xl mb-4">playlist_add</span>
                        <p class="text-xl font-bold">No playlists yet</p>
                        <p class="text-sm font-medium mt-2">Create your first playlist and start collecting your favorite
                            themes!</p>
                        <a href="{{ route('playlists.create') }}" class="mt-6 text-primary font-bold hover:underline">Create
                            Playlist</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection


