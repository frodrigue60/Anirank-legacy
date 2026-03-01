@extends('layouts.admin')

@section('title', 'Tournament View')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <a href="{{ route('admin.tournaments.index') }}"
                    class="text-blue-400 hover:text-white text-sm font-bold uppercase tracking-widest flex items-center mb-4 transition-colors">
                    <span class="material-symbols-outlined text-sm mr-1">arrow_back</span>
                    BACK
                </a>
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ $tournament->name }}</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">
                    {{ $tournament->size }} Songs • Status: {{ $tournament->status }}
                </p>
            </div>

            @if ($tournament->status === 'draft')
                <form action="{{ route('admin.tournaments.seed', $tournament->id) }}" method="post">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-green-900/20 active:scale-[0.98]">
                        <span class="material-symbols-outlined mr-2">play_arrow</span>
                        SEED & START
                    </button>
                </form>
            @endif
        </div>

        {{-- We will render the Livewire Bracket Component here later when building the frontend --}}
        @if ($tournament->status !== 'draft')
            <div
                class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden p-6 md:p-12 min-h-screen">
                <livewire:tournament-bracket :tournament="$tournament" />
            </div>
        @else
            <div>
                <livewire:admin.tournament-song-selector :tournament="$tournament" />
            </div>
        @endif

    </div>
@endsection
