<div
    class="relative bg-zinc-900/50 backdrop-blur-md border border-zinc-800 rounded-2xl w-full min-w-[280px] max-w-[320px] shadow-xl overflow-hidden flex flex-col group transition-all hover:border-zinc-700">

    {{-- Matchup Header --}}
    <div
        class="bg-zinc-950/80 px-4 py-2 border-b border-zinc-800 flex justify-between items-center text-xs text-zinc-500 font-mono">
        <span>Match #{{ $matchup->position }}</span>
        @if (!$matchup->is_active)
            <span class="text-red-500 font-bold uppercase">Closed</span>
        @else
            <span class="text-green-500 font-bold uppercase flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Active
            </span>
        @endif
    </div>

    {{-- Song 1 --}}
    <div @if ($matchup->is_active && !$hasVoted && $matchup->song1_id) wire:click="vote({{ $matchup->song1_id }})" @endif
        class="flex items-center gap-3 p-3 transition-colors relative 
        @if ($matchup->is_active && !$hasVoted && $matchup->song1_id) cursor-pointer hover:bg-zinc-800/80 @endif
        @if ($matchup->winner_song_id === $matchup->song1_id) bg-green-900/10 border-l-4 border-green-500 @else border-l-4 border-transparent @endif
        border-b border-zinc-800/50">
        @if ($matchup->song1)
            <img src="{{ $matchup->song1->anime->thumbnail_url }}"
                class="w-10 h-10 object-cover rounded-lg bg-zinc-800 shadow-sm" alt="Cover">
            <div class="flex-1 min-w-0">
                <h4 class="text-white font-bold text-sm truncate">{{ $matchup->song1->name }}</h4>
                <p class="text-zinc-500 text-[10px] uppercase tracking-widest truncate">
                    @foreach ($matchup->song1->artists as $artist)
                        {{ $artist->name }} {{ $loop->last ? '' : '&' }}
                    @endforeach
                </p>
            </div>
            <div class="text-right pl-2 flex flex-col items-end justify-center">
                @if ($matchup->tournament && $matchup->tournament->winner_song_id === $matchup->song1_id)
                    <span class="text-amber-500 mb-1" title="Tournament Champion">
                        <span class="material-symbols-outlined !text-[20px]">emoji_events</span>
                    </span>
                @endif
                <span
                    class="text-white font-mono font-bold text-lg bg-zinc-950 px-2 py-1 rounded-md">{{ $matchup->song1_votes }}</span>
            </div>
        @else
            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-600">
                <span class="material-symbols-outlined text-sm">help</span>
            </div>
            <div class="flex-1 text-zinc-600 font-bold text-sm italic">TBD</div>
        @endif

        {{-- Vote Status overlay --}}
        @if (
            $hasVoted &&
                \App\Models\TournamentVote::where('tournament_matchup_id', $matchup->id)->where('user_id', auth()->id())->where('song_id', $matchup->song1_id)->exists())
            <div
                class="absolute inset-y-0 right-14 flex items-center gap-1 text-blue-400 font-black uppercase text-[10px] tracking-widest bg-zinc-900/90 px-2 rounded-l-lg shadow-[-10px_0_15px_rgba(24,24,27,0.9)]">
                <span class="material-symbols-outlined text-sm">how_to_vote</span> Voted
            </div>
        @endif
    </div>

    {{-- Divider "VS" --}}
    <div
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-zinc-800 text-zinc-400 text-[10px] font-black italic border-2 border-zinc-900 rounded-full w-6 h-6 flex items-center justify-center shadow-lg z-10">
        VS
    </div>

    {{-- Song 2 --}}
    <div @if ($matchup->is_active && !$hasVoted && $matchup->song2_id) wire:click="vote({{ $matchup->song2_id }})" @endif
        class="flex items-center gap-3 p-3 transition-colors relative
        @if ($matchup->is_active && !$hasVoted && $matchup->song2_id) cursor-pointer hover:bg-zinc-800/80 @endif
        @if ($matchup->winner_song_id === $matchup->song2_id) bg-green-900/10 border-l-4 border-green-500 @else border-l-4 border-transparent @endif
        ">
        @if ($matchup->song2)
            <img src="{{ $matchup->song2->anime->thumbnail_url }}"
                class="w-10 h-10 object-cover rounded-lg bg-zinc-800 shadow-sm" alt="Cover">
            <div class="flex-1 min-w-0">
                <h4 class="text-white font-bold text-sm truncate">{{ $matchup->song2->name }}</h4>
                <p class="text-zinc-500 text-[10px] uppercase tracking-widest truncate">
                    @foreach ($matchup->song2->artists as $artist)
                        {{ $artist->name }} {{ $loop->last ? '' : '&' }}
                    @endforeach
                </p>
            </div>
            <div class="text-right pl-2 flex flex-col items-end justify-center">
                @if ($matchup->tournament && $matchup->tournament->winner_song_id === $matchup->song2_id)
                    <span class="text-amber-500 mb-1" title="Tournament Champion">
                        <span class="material-symbols-outlined !text-[20px]">emoji_events</span>
                    </span>
                @endif
                <span
                    class="text-white font-mono font-bold text-lg bg-zinc-950 px-2 py-1 rounded-md">{{ $matchup->song2_votes }}</span>
            </div>
        @else
            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-600">
                <span class="material-symbols-outlined text-sm">help</span>
            </div>
            <div class="flex-1 text-zinc-600 font-bold text-sm italic">TBD</div>
        @endif

        {{-- Vote Status overlay --}}
        @if (
            $hasVoted &&
                \App\Models\TournamentVote::where('tournament_matchup_id', $matchup->id)->where('user_id', auth()->id())->where('song_id', $matchup->song2_id)->exists())
            <div
                class="absolute inset-y-0 right-14 flex items-center gap-1 text-blue-400 font-black uppercase text-[10px] tracking-widest bg-zinc-900/90 px-2 rounded-l-lg shadow-[-10px_0_15px_rgba(24,24,27,0.9)]">
                <span class="material-symbols-outlined text-sm">how_to_vote</span> Voted
            </div>
        @endif
    </div>

</div>
