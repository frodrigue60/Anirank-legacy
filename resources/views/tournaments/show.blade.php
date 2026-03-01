@extends('layouts.app')

@section('title', $tournament->name . ' - Bracket | Anirank')

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-8">

        {{-- Tournament Header Info --}}
        <div class="mb-10 text-center animate-in fade-in slide-in-from-bottom-8 duration-700">
            <div
                class="inline-flex items-center justify-center p-3 sm:p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl sm:rounded-3xl mb-4 sm:mb-6 shadow-2xl shadow-amber-500/20">
                <span class="material-symbols-outlined text-3xl sm:text-5xl text-amber-500">emoji_events</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight mb-2 sm:mb-4 px-2">
                {{ $tournament->name }}
            </h1>
            @if ($tournament->description)
                <p class="text-sm sm:text-base text-white/60 max-w-2xl mx-auto px-4 leading-relaxed mb-4">
                    {{ $tournament->description }}
                </p>
            @endif
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-4 mt-4 sm:mt-6">
                <span
                    class="px-3 sm:px-4 py-1.5 sm:py-2 bg-surface-dark border border-white/10 rounded-full text-[10px] sm:text-xs font-bold text-white/60 uppercase tracking-widest shadow-xl flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">groups</span>
                    {{ $tournament->size }} Songs Bracket
                </span>
                <span
                    class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest border shadow-xl flex items-center gap-2
                @if ($tournament->status === 'active') bg-green-500/20 text-green-400 border-green-500/30
                @elseif($tournament->status === 'completed') bg-blue-500/20 text-blue-400 border-blue-500/30 @endif
            ">
                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">
                        @if ($tournament->status === 'active')
                            local_fire_department
                        @else
                            verified
                        @endif
                    </span>
                    {{ $tournament->status }}
                </span>
            </div>
        </div>

        {{-- The Livewire Bracket component takes care of rendering the tree, the matchups, and real-time interactions --}}
        <livewire:tournament-bracket :tournament="$tournament" />
    </div>
@endsection
