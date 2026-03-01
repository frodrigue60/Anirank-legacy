<div class="w-full overflow-x-auto pb-16 pt-8 custom-scrollbar relative" wire:poll.30s>
    <div class="flex items-stretch justify-start min-w-max gap-16 px-8 py-16">
        @foreach ($rounds as $roundNumber => $matchups)
            <div class="flex flex-col relative w-[320px]">

                {{-- Round Header --}}
                <div
                    class="absolute -top-12 left-0 right-0 text-center font-black text-white/30 uppercase tracking-[0.3em] text-xs">
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
                            <div class="absolute right-full w-8 border-t-2 border-zinc-700/50 top-1/2 z-0"></div>
                        @endif

                        @if (!$loop->parent->last)
                            <!-- Line pointing out from card towards next round -->
                            <div class="absolute left-full w-8 border-t-2 border-zinc-700/50 top-1/2 z-0"></div>
                        @endif

                        {{-- The interactive voting card for a specific matchup duel --}}
                        <div class="relative z-10 w-full py-4">
                            <livewire:matchup-card :matchup="$matchup" :key="'matchup-' . $matchup->id" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
