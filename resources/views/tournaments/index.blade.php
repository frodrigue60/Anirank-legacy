@extends('layouts.app')

@section('title', 'Tournaments - Anirank')

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-8">
        {{-- Header Content --}}
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                Song <span class="text-amber-500">Tournaments</span>
            </h1>
            <p class="text-lg text-white/60 max-w-2xl">
                Vote for your favorite songs in elimination brackets. Only the best theme will survive and claim the top
                spot.
            </p>
        </div>

        {{-- Tournaments Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($tournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament->slug) }}"
                    class="group block relative rounded-3xl overflow-hidden glass-panel border border-white/10 hover:border-amber-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/20 hover:-translate-y-1">
                    {{-- Decorative background gradient --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    </div>

                    <div class="p-6 relative z-10 flex flex-col h-full min-h-[220px]">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="p-3 bg-surface-dark rounded-2xl border border-white/5 text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[24px]">emoji_events</span>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest border
                            @if ($tournament->status === 'active') bg-green-500/20 text-green-400 border-green-500/30
                            @elseif($tournament->status === 'completed') bg-blue-500/20 text-blue-400 border-blue-500/30
                            @else bg-zinc-500/20 text-zinc-400 border-zinc-500/30 @endif
                        ">
                                {{ $tournament->status }}
                            </span>
                        </div>

                        <h2
                            class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-amber-400 transition-colors line-clamp-2">
                            {{ $tournament->name }}
                        </h2>

                        @if ($tournament->description)
                            <p class="text-sm text-white/50 line-clamp-2 mb-4">
                                {{ $tournament->description }}
                            </p>
                        @endif

                        <div
                            class="mt-auto flex items-center justify-between text-xs font-medium text-white/40 uppercase tracking-widest pt-4 border-t border-white/5">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">groups</span>
                                {{ $tournament->size }} Songs
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">event</span>
                                {{ $tournament->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div
                    class="col-span-full py-16 text-center bg-surface-darker/50 rounded-3xl border border-white/5 border-dashed">
                    <span class="material-symbols-outlined text-6xl text-white/20 mb-4 block">event_busy</span>
                    <h3 class="text-xl font-bold text-white mb-2">No Tournaments Yet</h3>
                    <p class="text-white/50">There are currently no active or completed tournaments to display.</p>
                </div>
            @endforelse
        </div>

        @if ($tournaments->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $tournaments->links() }}
            </div>
        @endif
    </div>
@endsection
