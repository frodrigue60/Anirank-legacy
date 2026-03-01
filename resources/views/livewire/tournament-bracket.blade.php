<div class="w-full relative" wire:poll.30s>
    {{-- Header Options: View Toggle --}}
    <div
        class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4 bg-zinc-900/40 p-3 rounded-2xl border border-zinc-800">
        <h2 class="text-xl font-bold text-white px-2 tracking-wide font-mono uppercase">{{ $tournament->name }}</h2>
        <div class="flex bg-zinc-950 p-1 rounded-xl border border-zinc-800 gap-1">
            <button wire:click="setViewMode('list')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $viewMode === 'list' ? 'bg-zinc-800 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300' }}">
                <span class="material-symbols-outlined text-sm">format_list_bulleted</span> List View
            </button>
            <button wire:click="setViewMode('tree')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $viewMode === 'tree' ? 'bg-zinc-800 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300' }}">
                <span class="material-symbols-outlined text-sm">account_tree</span> Tree View
            </button>
        </div>
    </div>

    @if ($viewMode === 'list')
        {{-- List View Layout --}}

        {{-- Round Navigation --}}
        <h3 class="text-zinc-500 text-xs tracking-[0.2em] uppercase font-bold mb-4 font-mono">Select Round</h3>
        <div class="flex flex-wrap gap-2 mb-8 bg-zinc-900/50 p-2 rounded-xl border border-zinc-800/80">
            @foreach ($rounds as $roundNumber => $matchups)
                <button wire:click="setRound({{ $roundNumber }})"
                    class="flex-1 min-w-[120px] py-3 px-4 rounded-lg text-sm font-bold transition-colors border {{ $activeRound == $roundNumber ? 'bg-indigo-600/20 text-indigo-400 border-indigo-500/30' : 'bg-zinc-950 text-zinc-500 border-zinc-800 hover:bg-zinc-800/50 hover:text-white' }}">
                    @if ($roundNumber == 2)
                        Finals
                    @elseif($roundNumber == 4)
                        Semifinals
                    @elseif($roundNumber == 8)
                        Quarterfinals
                    @else
                        Round of {{ $roundNumber }}
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Group Tabs (If applicable) --}}
        @if (!empty($groups))
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-zinc-800">
                <h3 class="text-white text-sm tracking-widest uppercase font-bold font-mono">
                    @if ($activeRound == 2)
                        Finals
                    @elseif($activeRound == 4)
                        Semifinals
                    @elseif($activeRound == 8)
                        Quarterfinals
                    @else
                        Round of {{ $activeRound }}
                    @endif
                </h3>
                <div class="flex gap-2">
                    <span
                        class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mr-2 self-center">Group:</span>
                    @foreach (array_keys($groups) as $letter)
                        <button wire:click="setGroup('{{ $letter }}')"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors {{ $activeGroup === $letter ? 'bg-green-600 text-white shadow-[0_0_15px_rgba(22,163,74,0.4)]' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' }}">
                            {{ $letter }}
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <h3
                class="text-white text-sm tracking-widest uppercase font-bold font-mono mb-6 pb-2 border-b border-zinc-800">
                @if ($activeRound == 2)
                    Finals
                @elseif($activeRound == 4)
                    Semifinals
                @elseif($activeRound == 8)
                    Quarterfinals
                @else
                    Round of {{ $activeRound }}
                @endif
            </h3>
        @endif

        {{-- Matchups List --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($activeMatchups as $matchup)
                <livewire:matchup-card :matchup="$matchup" :key="'list-matchup-' . $matchup->id" />
            @empty
                <div class="col-span-full py-12 text-center text-zinc-500 italic">No matchups available for this round.
                </div>
            @endforelse
        </div>
    @else
        {{-- Classic Tree View Layout --}}
        <div
            class="w-full overflow-x-auto pb-16 pt-8 custom-scrollbar border border-zinc-800/50 rounded-2xl bg-zinc-950/20">
            <div class="flex items-stretch justify-start min-w-max gap-16 px-8 py-16">
                @foreach ($rounds as $roundNumber => $matchups)
                    <div class="flex flex-col relative w-[320px]">

                        {{-- Round Header --}}
                        <div
                            class="absolute -top-12 left-0 right-0 text-center font-black text-white/30 uppercase tracking-[0.3em] text-xs font-mono">
                            @if ($roundNumber == 2)
                                Final
                            @elseif($roundNumber == 4)
                                Semi-Finals
                            @elseif($roundNumber == 8)
                                Quarter-Finals
                            @elseif($roundNumber == 16)
                                Round of 16
                            @elseif($roundNumber == 32)
                                Round of 32
                            @else
                                Round of {{ $roundNumber }}
                            @endif
                        </div>

                        @foreach ($matchups->sortBy('position') as $matchup)
                            <div class="flex-1 flex flex-col justify-center relative w-full group">

                                {{-- Visual Connection Lines --}}
                                @if ($loop->parent->iteration > 1)
                                    <!-- Bracket `]` shape connecting parents -->
                                    <div class="absolute right-full mr-8 w-8 border-y-2 border-r-2 border-zinc-700/50 rounded-r-xl z-0"
                                        style="top: 25%; bottom: 25%;"></div>
                                    <!-- Bracket horizontal stem joining the card -->
                                    <div class="absolute right-full w-8 border-t-2 border-zinc-700/50 top-1/2 z-0">
                                    </div>
                                @endif

                                @if (!$loop->parent->last)
                                    <!-- Line pointing out from card towards next round -->
                                    <div class="absolute left-full w-8 border-t-2 border-zinc-700/50 top-1/2 z-0"></div>
                                @endif

                                {{-- The interactive voting card for a specific matchup duel --}}
                                <div class="relative z-10 w-full py-4">
                                    <livewire:matchup-card :matchup="$matchup" :key="'tree-matchup-' . $matchup->id" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
