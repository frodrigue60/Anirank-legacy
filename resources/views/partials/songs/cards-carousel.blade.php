@foreach ($songs as $song)
    <div
        class="group relative w-80 h-40 rounded-xl overflow-hidden border border-white/5 shrink-0 cursor-pointer
                hover:border-primary/30 transition-all duration-300">
        {{-- Background --}}
        <x-ui.image :src="$song->anime->thumbnail_url" :alt="$song->name"
            class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:scale-105
                   transition-transform duration-500"
            style="filter: brightness(0.4);" />

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-linear-to-r from-background-dark via-background-dark/80 to-transparent"></div>

        {{-- Content --}}
        <div class="relative h-full p-4 flex items-center justify-between">
            <div class="space-y-1 min-w-0 flex-1">
                <span
                    class="inline-block text-[10px] font-bold uppercase tracking-wider bg-primary/80 px-1.5 py-0.5 rounded text-white">
                    {{ $song->slug }}
                </span>
                <p class="text-sm font-bold text-white truncate group-hover:text-primary transition-colors">
                    <a href="{{ $song->url }}">{{ $song->name }}</a>
                </p>
                <p class="text-xs text-white/50 truncate">
                    @foreach ($song->artists as $artist)
                        {{ $artist->name }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </p>
                <p class="text-[11px] text-white/25 truncate italic">{{ $song->anime->title }}</p>
            </div>

            <div class="flex flex-col items-end gap-2 shrink-0 ml-3">
                @php
                    $user = auth()->user();
                    $format = $user?->score_format ?? 'POINT_100';
                    $score = $song->formattedAvgScore($format);
                @endphp
                <div class="flex items-center gap-1 text-yellow-400 text-xs font-bold">
                    <span class="material-symbols-outlined filled text-sm">star</span>
                    {{ $score }}
                </div>
                <a href="{{ $song->url }}"
                    class="flex items-center justify-center h-9 w-9 rounded-full bg-white/10
                           hover:bg-primary transition-all text-white backdrop-blur-sm
                           border border-white/10 group-hover:border-primary/50">
                    <span class="material-symbols-outlined text-lg">play_arrow</span>
                </a>
            </div>
        </div>
    </div>
@endforeach
