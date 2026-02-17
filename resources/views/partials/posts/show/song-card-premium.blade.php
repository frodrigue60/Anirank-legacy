<div
    class="group relative bg-surface-darker/50 p-4 rounded-xl hover:bg-surface-dark transition-all duration-300 border border-white/5 flex gap-5 items-center">
    {{-- Theme Number / Index --}}
    <div
        class="flex flex-col items-center justify-center w-12 h-12 bg-primary/10 rounded-xl border border-primary/20 group-hover:bg-primary group-hover:border-primary transition-all duration-300 shadow-lg shadow-primary/5 shrink-0">
        <span
            class="text-[10px] uppercase font-black text-primary group-hover:text-white tracking-widest leading-none mb-0.5">
            {{ $song->type }}
        </span>
        <span class="text-lg font-black text-white leading-none">
            {{ $song->theme_num ?? $song->number }}
        </span>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-4">
            <h3 class="font-bold text-white truncate text-lg group-hover:text-primary transition-colors mb-0.5"
                title="{{ $song->title }}">
                {{ $song->name }}
            </h3>
        </div>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
            <p class="text-sm text-white/40 font-medium truncate">
                @foreach ($song->artists as $artist)
                    <a href="{{ route('artists.show', $artist) }}"
                        class="hover:text-primary transition-colors">{{ $artist->name }}</a>{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        @auth
            <button type="button" onclick="Livewire.dispatch('openReportModal', { songId: {{ $song->id }} })"
                title="Report Issue"
                class="w-10 h-10 flex items-center justify-center bg-white/5 hover:bg-red-500/20 text-white/40 hover:text-red-500 rounded-full border border-white/5 transition-all">
                <span class="material-symbols-outlined text-[18px]">report</span>
            </button>
        @endauth

        <a href="{{ $song->url }}"
            class="w-10 h-10 flex items-center justify-center bg-primary rounded-full text-white shadow-lg shadow-primary/20 hover:scale-110 active:scale-95 transition-all">
            <span class="material-symbols-outlined filled text-2xl">play_arrow</span>
        </a>
    </div>
</div>
</div>
