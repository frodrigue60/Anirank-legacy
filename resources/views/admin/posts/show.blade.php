@extends('layouts.admin')

@section('title', 'Post Details')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ $post->title }}</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Entry Metadata & Content</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.posts.edit', $post->id) }}"
                    class="inline-flex items-center px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all border border-zinc-800 hover:border-zinc-700 shadow-xl">
                    <span class="material-symbols-outlined mr-2">edit</span>
                    EDIT ENTRY
                </a>

                <a href="{{ route('admin.posts.songs', $post->id) }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">music_note</span>
                    MANAGE SONGS
                </a>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Visual Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="aspect-[3/4] relative">
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}"
                            class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white shadow-lg">
                                {{ $post->status == 1 ? 'PUBLISHED' : 'DRAFT' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-xs border-b border-zinc-800 pb-3">
                            <span class="text-zinc-500 font-bold uppercase">Year</span>
                            <span class="text-white font-mono">{{ $post->year->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs border-b border-zinc-800 pb-3">
                            <span class="text-zinc-500 font-bold uppercase">Season</span>
                            <span class="text-white font-mono">{{ $post->season->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-bold uppercase">Songs</span>
                            <span class="text-white font-mono">{{ $post->songs->count() }} entries</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Synopsis & Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-6 flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">subject</span> SYNOPSIS
                    </h3>
                    <div
                        class="prose prose-invert max-w-none text-zinc-300 leading-relaxed italic border-l-4 border-zinc-800 pl-6 py-2">
                        {!! $post->description !!}
                    </div>
                </div>

                @if ($post->banner)
                    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-4 shadow-xl">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-4 px-4 flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">landscape</span> BANNER ASSET
                        </h3>
                        <div class="rounded-2xl overflow-hidden aspect-[21/9] border border-zinc-800">
                            <img src="{{ Storage::url($post->banner) }}" alt="" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
