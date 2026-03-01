<div class="space-y-6">
    <div class="flex flex-col md:flex-row gap-6">

        {{-- Left Pane: Search & Available Themes --}}
        <div class="flex-1 bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col h-[800px]">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-500">library_music</span>
                    Available Themes
                </h2>
                <div class="bg-purple-500/20 text-purple-400 text-xs font-bold px-3 py-1 rounded-full">
                    {{ number_format($totalSongsAvailable) }} TOTAL
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="relative mb-6">
                <span
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 material-symbols-outlined">search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl pl-12 pr-4 py-3 focus:outline-hidden focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all placeholder-zinc-600"
                    placeholder="Search by anime series or song title...">
            </div>

            {{-- Type Filters --}}
            <div class="flex gap-2 mb-6 overflow-x-auto pb-2 custom-scrollbar">
                @foreach (['All', 'OP', 'ED'] as $type)
                    <button wire:click="$set('typeFilter', '{{ $type }}')"
                        class="px-4 py-1.5 rounded-full text-sm font-bold whitespace-nowrap transition-all
                        {{ $typeFilter === $type ? 'bg-purple-600 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' }}">
                        {{ $type === 'All' ? 'All' : ($type === 'OP' ? 'Openings' : 'Endings') }}
                    </button>
                @endforeach
            </div>

            {{-- Results List --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pr-2">
                @forelse($availableSongs as $song)
                    @php
                        $isSelected = in_array($song->id, $selectedSongIds);
                        $isPlaying = $previewingSongId === $song->id;
                    @endphp

                    <div
                        class="flex items-center gap-4 p-3 rounded-xl border transition-all
                        {{ $isSelected ? 'bg-purple-900/10 border-purple-500/50' : 'bg-zinc-950/50 border-zinc-800/50 hover:border-zinc-700 hover:bg-zinc-900' }}">

                        {{-- Thumbnail & Preview Play --}}
                        <div class="relative w-16 h-16 rounded-lg overflow-hidden shrink-0 group cursor-pointer"
                            wire:click="previewSong({{ $song->id }})">
                            <img src="{{ $song->anime->thumbnail_url }}"
                                class="w-full h-full object-cover transition-transform group-hover:scale-110"
                                alt="Cover">

                            {{-- Overlay Play Button --}}
                            <div
                                class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity {{ $isPlaying ? '!opacity-100 bg-black/60' : '' }}">
                                <span class="material-symbols-outlined text-white text-2xl">
                                    {{ $isPlaying ? 'pause_circle' : 'play_circle' }}
                                </span>
                            </div>

                            {{-- Active playing indicator --}}
                            @if ($isPlaying)
                                <div class="absolute bottom-1 left-0 right-0 flex justify-center gap-0.5 px-2">
                                    <div class="w-1 bg-white h-2 rounded-full animate-pulse"></div>
                                    <div class="w-1 bg-white h-3 rounded-full animate-pulse"
                                        style="animation-delay: 0.1s"></div>
                                    <div class="w-1 bg-white h-1.5 rounded-full animate-pulse"
                                        style="animation-delay: 0.2s"></div>
                                </div>
                            @endif
                        </div>

                        {{-- Song Info --}}
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-white font-bold text-sm truncate {{ $isSelected ? 'text-purple-400' : '' }}">
                                {{ $song->name }}</h4>
                            <p class="text-zinc-500 text-xs truncate">{{ $song->anime->title }}</p>

                            <div class="flex items-center gap-2 mt-1.5">
                                <span
                                    class="text-[10px] font-bold px-1.5 py-0.5 rounded-md 
                                    {{ $song->type === 'OP' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ $song->type }}
                                </span>
                                <span class="text-zinc-400 text-xs flex items-center gap-1">
                                    <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                    {{ $song->formattedAvgScore() }}
                                </span>
                            </div>
                        </div>

                        {{-- Add Button --}}
                        <button wire:click="toggleSong({{ $song->id }})"
                            @if (!$isSelected && count($selectedSongIds) >= $tournament->size) disabled @endif
                            class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all border
                            {{ $isSelected
                                ? 'bg-purple-500/20 border-purple-500 text-purple-400'
                                : 'bg-zinc-800 border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-500 disabled:opacity-50 disabled:cursor-not-allowed' }}">
                            <span class="material-symbols-outlined text-xl">
                                {{ $isSelected ? 'check' : 'add' }}
                            </span>
                        </button>
                    </div>

                    {{-- Active Mini Player Inline Hook --}}
                    @if ($isPlaying)
                        <div class="w-full bg-black/50 border border-purple-500/30 rounded-xl p-2 mt-[-8px] mb-2">
                            @php
                                $firstVariant = $song->songVariants->first();
                            @endphp
                            @if ($firstVariant && $firstVariant->video)
                                @php
                                    $hasValidFile = false;
                                    $embedCode = $firstVariant->video->embed_code;

                                    if ($firstVariant->video->type === 'file' && $firstVariant->video->video_src) {
                                        try {
                                            $hasValidFile = \Illuminate\Support\Facades\Storage::disk(
                                                $firstVariant->video->disk,
                                            )->exists($firstVariant->video->video_src);
                                        } catch (\Exception $e) {
                                            $hasValidFile = false;
                                        }
                                    } elseif (!empty($embedCode)) {
                                        // Inject autoplay into the iframe src attribute
                                        if (str_contains($embedCode, '?')) {
                                            // $embedCode = str_replace('"', '&autoplay=1"', $embedCode); // very rough heuristic, better to target src specifically
                                            // A safer replacement targeting src="...":
                                            $embedCode = preg_replace(
                                                '/src="([^"]+)\?([^"]+)"/',
                                                'src="$1?$2&autoplay=1"',
                                                $embedCode,
                                            );
                                            $embedCode = preg_replace(
                                                '/src="([^"]+)"(?!.*\?)/',
                                                'src="$1?autoplay=1"',
                                                $embedCode,
                                            );
                                        } else {
                                            $embedCode = preg_replace(
                                                '/src="([^"]+)"/',
                                                'src="$1?autoplay=1"',
                                                $embedCode,
                                            );
                                        }
                                    }
                                @endphp
                                @if ($hasValidFile)
                                    <audio controls autoplay class="w-full h-8"
                                        onended="@this.set('previewingSongId', null)">
                                        <source
                                            src="{{ $firstVariant->video->local_url ?? $firstVariant->video->video_src }}"
                                            type="video/webm">
                                        Your browser does not support the audio element.
                                    </audio>
                                @elseif(!empty($embedCode))
                                    <div
                                        class="w-full h-24 rounded overflow-hidden [&>iframe]:w-full [&>iframe]:h-full pointer-events-auto">
                                        {!! $embedCode !!}
                                    </div>
                                @else
                                    <div
                                        class="text-xs text-red-500 font-bold text-center py-2 flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-sm">music_off</span>
                                        No preview available for this theme
                                    </div>
                                @endif
                            @else
                                <div
                                    class="text-xs text-red-500 font-bold text-center py-2 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-sm">music_off</span>
                                    No preview available for this theme
                                </div>
                            @endif
                        </div>
                    @endif

                @empty
                    <div class="text-center py-12 text-zinc-500">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">search_off</span>
                        <p>No themes matched your search.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- Divider / Decorative --}}
        <div class="hidden md:flex flex-col justify-center items-center gap-4 text-purple-500/50">
            <span
                class="material-symbols-outlined bg-zinc-900 border border-zinc-800 rounded-full w-10 h-10 flex items-center justify-center">chevron_right</span>
        </div>

        {{-- Right Pane: Selected Bracket Pool --}}
        <div class="flex-1 bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col h-[800px]">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-500">emoji_events</span>
                    Selected Bracket
                </h2>
                <div
                    class="bg-purple-500 text-white shadow-lg shadow-purple-500/20 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap
                    {{ count($selectedSongIds) === $tournament->size ? 'bg-green-500 shadow-green-500/20' : '' }}">
                    {{ count($selectedSongIds) }} / {{ $tournament->size }}
                </div>
            </div>

            {{-- Progress Indicator --}}
            <div class="mb-6 bg-zinc-950 border border-zinc-800 rounded-xl p-4">
                <div class="flex justify-between text-xs font-bold mb-2">
                    <span class="text-purple-400 uppercase tracking-widest">Bracket Progress</span>
                    <span
                        class="{{ count($selectedSongIds) === $tournament->size ? 'text-green-400' : 'text-zinc-500' }}">
                        {{ round((count($selectedSongIds) / $tournament->size) * 100) }}%
                    </span>
                </div>
                <div class="w-full h-2 bg-zinc-900 rounded-full overflow-hidden">
                    <div class="h-full bg-linear-to-r from-purple-600 to-indigo-500 transition-all duration-500
                        {{ count($selectedSongIds) === $tournament->size ? 'from-green-500 to-emerald-400' : '' }}"
                        style="width: {{ (count($selectedSongIds) / $tournament->size) * 100 }}%">
                    </div>
                </div>
            </div>

            {{-- Pool Items --}}
            <div
                class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pr-2 border border-dashed border-zinc-800/50 rounded-xl p-2 relative">
                @if (count($selectedSongIds) === 0)
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-zinc-600">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-30">add</span>
                        <p class="text-sm font-medium">Add more themes from the left</p>
                    </div>
                @else
                    @foreach ($selectedSongs as $song)
                        <div
                            class="flex items-center justify-between p-3 rounded-xl bg-zinc-950 border border-zinc-800 group hover:border-red-500/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $song->anime->thumbnail_url }}"
                                    class="w-10 h-10 rounded-md object-cover opacity-80" alt="Cover">
                                <div class="min-w-0">
                                    <h5 class="text-white text-sm font-bold truncate">{{ $song->name }}</h5>
                                    <p class="text-zinc-500 text-xs truncate">{{ $song->anime->title }}</p>
                                </div>
                            </div>
                            <button wire:click="toggleSong({{ $song->id }})"
                                class="text-red-500/50 hover:text-red-500 px-2 group-hover:block transition-all"
                                title="Remove">
                                <span class="material-symbols-outlined text-lg">do_not_disturb_on</span>
                            </button>
                        </div>
                    @endforeach

                    @if (count($selectedSongIds) < $tournament->size)
                        <div
                            class="flex items-center justify-center p-4 rounded-xl border border-dashed border-zinc-800 text-zinc-600 cursor-default">
                            <div class="text-center">
                                <span class="material-symbols-outlined mb-1">add</span>
                                <p class="text-xs">Add {{ $tournament->size - count($selectedSongIds) }} more themes
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Footer Action Buttons --}}
            <div class="mt-6 flex gap-4">
                <button wire:click="clearAll"
                    class="px-6 py-3 rounded-xl font-bold bg-zinc-950 border border-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors">
                    Clear All
                </button>
                <button wire:click="finalizeBracket" @if (count($selectedSongIds) !== $tournament->size) disabled @endif
                    class="flex-1 py-3 rounded-xl font-bold transition-all shadow-lg text-center
                    {{ count($selectedSongIds) === $tournament->size
                        ? 'bg-purple-600 hover:bg-purple-500 text-white shadow-purple-500/20'
                        : 'bg-zinc-800 text-zinc-500 cursor-not-allowed border border-zinc-700' }}">
                    Finalize List
                </button>
            </div>

        </div>
    </div>
</div>
