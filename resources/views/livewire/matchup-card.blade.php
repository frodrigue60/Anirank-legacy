<div
    class="relative bg-zinc-900/50 backdrop-blur-md border border-zinc-800 rounded-2xl w-full min-w-[280px] md:max-w-md mx-auto shadow-xl overflow-hidden flex flex-col group transition-all hover:border-zinc-700">

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
    @php
        $isSong1Winner = $matchup->winner_song_id === $matchup->song1_id;
        $isSong1Loser = !$matchup->is_active && $matchup->winner_song_id && !$isSong1Winner;
    @endphp

    <div @if ($matchup->is_active && !$hasVoted && $matchup->song1_id) wire:click="vote({{ $matchup->song1_id }})" @endif
        class="flex items-center gap-3 p-3 transition-colors relative 
        @if ($matchup->is_active && !$hasVoted && $matchup->song1_id) cursor-pointer hover:bg-zinc-800/80 @endif
        @if ($isSong1Winner) bg-green-900/10 border-l-4 border-green-500 @else border-l-4 border-transparent @endif
        border-b border-zinc-800/50">

        @if ($matchup->song1)
            <div class="relative">
                <img src="{{ $matchup->song1->anime->thumbnail_url ?? asset('images/default-anime.jpg') }}"
                    class="w-12 h-12 object-cover rounded-lg bg-zinc-800 shadow-sm transition-all {{ $isSong1Loser ? 'opacity-40 grayscale' : '' }}"
                    alt="Cover">
                @if ($isSong1Winner)
                    <div
                        class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg border-2 border-zinc-900 z-10">
                        <span class="material-symbols-outlined text-[14px]">military_tech</span>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0 {{ $isSong1Loser ? 'opacity-50' : '' }}">
                <h4 class="text-white font-bold text-sm truncate">{{ $matchup->song1->name }}</h4>
                <div class="flex items-center gap-1 text-[10px] text-zinc-500 uppercase tracking-wider truncate mb-0.5">
                    @foreach ($matchup->song1->artists as $artist)
                        <span>{{ $artist->name }}</span>{{ $loop->last ? '' : '&' }}
                    @endforeach
                </div>
                <div class="text-[10px] text-zinc-600 truncate">{{ $matchup->song1->anime->title }}</div>
            </div>

            <div
                class="text-right pl-2 flex flex-col items-end justify-center gap-1 {{ $isSong1Loser ? 'opacity-60 grayscale' : '' }}">
                <div class="flex items-center gap-1 text-zinc-400 text-xs font-mono bg-zinc-900/50 px-2 py-0.5 rounded border border-zinc-800"
                    title="Seed Position">
                    <span class="material-symbols-outlined text-[14px]">emoji_events</span>
                    <span>#{{ $matchup->song1_id }}</span> {{-- Replacing with ID temporarily if real Seed isn't available --}}
                </div>
                <div
                    class="flex items-center gap-1 text-white text-sm font-bold font-mono bg-zinc-950 px-2 py-0.5 rounded shadow-inner">
                    <span class="text-zinc-500 material-symbols-outlined text-[14px]">group</span>
                    <span>{{ $matchup->song1_votes }}</span>
                </div>
            </div>
        @else
            <div class="w-12 h-12 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-600">
                <span class="material-symbols-outlined text-sm">help</span>
            </div>
            <div class="flex-1 text-zinc-600 font-bold text-sm italic">TBD</div>
        @endif

        {{-- Vote Status overlay --}}
        @if (
            $hasVoted &&
                \App\Models\TournamentVote::where('tournament_matchup_id', $matchup->id)->where('user_id', auth()->id())->where('song_id', $matchup->song1_id)->exists())
            <div
                class="absolute inset-y-0 right-20 flex items-center gap-1 text-blue-400 font-black uppercase text-[10px] tracking-widest bg-zinc-900/90 px-2 rounded-l-lg shadow-[-10px_0_15px_rgba(24,24,27,0.9)] z-20">
                <span class="material-symbols-outlined text-sm">how_to_vote</span> Voted
            </div>
        @endif
    </div>

    {{-- Divider "VS" --}}
    <div
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-zinc-800 text-zinc-400 text-[10px] font-black italic border-2 border-zinc-900 rounded-full w-6 h-6 flex items-center justify-center shadow-lg z-30">
        VS
    </div>

    {{-- Song 2 --}}
    @php
        $isSong2Winner = $matchup->winner_song_id === $matchup->song2_id;
        $isSong2Loser = !$matchup->is_active && $matchup->winner_song_id && !$isSong2Winner;
    @endphp

    <div @if ($matchup->is_active && !$hasVoted && $matchup->song2_id) wire:click="vote({{ $matchup->song2_id }})" @endif
        class="flex items-center gap-3 p-3 transition-colors relative
        @if ($matchup->is_active && !$hasVoted && $matchup->song2_id) cursor-pointer hover:bg-zinc-800/80 @endif
        @if ($isSong2Winner) bg-green-900/10 border-l-4 border-green-500 @else border-l-4 border-transparent @endif
        ">

        @if ($matchup->song2)
            <div class="relative">
                <img src="{{ $matchup->song2->anime->thumbnail_url ?? asset('images/default-anime.jpg') }}"
                    class="w-12 h-12 object-cover rounded-lg bg-zinc-800 shadow-sm transition-all {{ $isSong2Loser ? 'opacity-40 grayscale' : '' }}"
                    alt="Cover">
                @if ($isSong2Winner)
                    <div
                        class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg border-2 border-zinc-900 z-10">
                        <span class="material-symbols-outlined text-[14px]">military_tech</span>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0 {{ $isSong2Loser ? 'opacity-50' : '' }}">
                <h4 class="text-white font-bold text-sm truncate">{{ $matchup->song2->name }}</h4>
                <div class="flex items-center gap-1 text-[10px] text-zinc-500 uppercase tracking-wider truncate mb-0.5">
                    @foreach ($matchup->song2->artists as $artist)
                        <span>{{ $artist->name }}</span>{{ $loop->last ? '' : '&' }}
                    @endforeach
                </div>
                <div class="text-[10px] text-zinc-600 truncate">{{ $matchup->song2->anime->title }}</div>
            </div>

            <div
                class="text-right pl-2 flex flex-col items-end justify-center gap-1 {{ $isSong2Loser ? 'opacity-60 grayscale' : '' }}">
                <div class="flex items-center gap-1 text-zinc-400 text-xs font-mono bg-zinc-900/50 px-2 py-0.5 rounded border border-zinc-800"
                    title="Seed Position">
                    <span class="material-symbols-outlined text-[14px]">emoji_events</span>
                    <span>#{{ $matchup->song2_id }}</span> {{-- Replacing with ID temporarily if real Seed isn't available --}}
                </div>
                <div
                    class="flex items-center gap-1 text-white text-sm font-bold font-mono bg-zinc-950 px-2 py-0.5 rounded shadow-inner">
                    <span class="text-zinc-500 material-symbols-outlined text-[14px]">group</span>
                    <span>{{ $matchup->song2_votes }}</span>
                </div>
            </div>
        @else
            <div class="w-12 h-12 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-600">
                <span class="material-symbols-outlined text-sm">help</span>
            </div>
            <div class="flex-1 text-zinc-600 font-bold text-sm italic">TBD</div>
        @endif

        {{-- Vote Status overlay --}}
        @if (
            $hasVoted &&
                \App\Models\TournamentVote::where('tournament_matchup_id', $matchup->id)->where('user_id', auth()->id())->where('song_id', $matchup->song2_id)->exists())
            <div
                class="absolute inset-y-0 right-20 flex items-center gap-1 text-blue-400 font-black uppercase text-[10px] tracking-widest bg-zinc-900/90 px-2 rounded-l-lg shadow-[-10px_0_15px_rgba(24,24,27,0.9)] z-20">
                <span class="material-symbols-outlined text-sm">how_to_vote</span> Voted
            </div>
        @endif
    </div>

</div>
