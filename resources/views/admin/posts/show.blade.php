@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Show Post</h1>
            <p class="text-zinc-400 mt-1"><span class="text-blue-400 font-semibold">{{ $post->title }}</span></p>
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
                        <i class="fa-solid fa-align-left mr-2 text-blue-500"></i> SYNOPSIS
                    </h3>
                    <div
                        class="prose prose-invert max-w-none text-zinc-300 leading-relaxed italic border-l-4 border-zinc-800 pl-6 py-2">
                        {!! $post->description !!}
                    </div>
                </div>

                @if ($post->banner)
                    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-4 shadow-xl">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-4 px-4 flex items-center">
                            <i class="fa-solid fa-panorama mr-2 text-blue-500"></i> BANNER ASSET
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
