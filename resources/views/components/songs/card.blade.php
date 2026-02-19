@props(['song'])

@php
    $user = auth()->user();
    $format = $user?->score_format ?? 'POINT_100';

    $score = $song->score ?? $song->formattedAvgScore($format);
    $userScore = $song->userScore ?? ($user ? $song->formattedUserScore($format, $user->id) : null);
    $url = $song->url ?? route('songs.show.nested', [$song->post->slug, $song->slug]);
    $number = $song->theme_num ?? $song->number;
@endphp

<div
    {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-xl h-48 card-hover transition-all duration-300 border border-white/5 bg-surface-dark/30']) }}>
    {{-- Background Image --}}
    <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105">
        <x-ui.image :src="$song->post->banner_url" class="w-full h-full object-cover opacity-40 shadow-inner"
            style="filter: brightness(0.4);" fallback="default-banner.webp" />
    </div>

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-linear-to-r from-background-dark via-background-dark/80 to-transparent">
    </div>

    {{-- Content --}}
    <div class="relative h-full p-6 flex items-center justify-between">
        <div class="space-y-1">
            <div
                class="inline-flex items-center px-2 py-0.5 rounded bg-primary text-[10px] font-bold text-white mb-2 uppercase tracking-wider">
                {{ $song->type }}{{ $number }}</div>
            <h3 class="text-xl font-bold text-white group-hover:text-primary transition-colors text-glow line-clamp-1">
                {{ $song->name }}</h3>
            <div class="text-slate-300 text-sm font-medium line-clamp-1">
                @foreach ($song->artists as $artist)
                    <span class="hover:text-primary transition-colors cursor-pointer">{{ $artist->name }}</span>
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </div>
            <div class="text-slate-500 text-xs italic mt-2 line-clamp-1">
                {{ $song->post->title }}</div>
        </div>

        <div class="flex flex-col items-end gap-2 shrink-0">
            <div class="glass px-3 py-2 rounded-lg border-primary/30 flex items-center gap-1.5 shadow-lg">
                <span class="material-symbols-outlined text-primary text-sm fill-1">star</span>
                <span class="text-white font-bold text-lg">{{ $score }}</span>
            </div>
            @if (isset($userScore))
                <div class="text-xs text-white/40 uppercase font-black tracking-widest mt-1">
                    Your Score: <span class="text-primary">{{ $userScore }}</span>
                </div>
            @endif
            <a href="{{ $url }}"
                class="mt-4 flex items-center justify-center h-10 w-10 rounded-full bg-white/10 hover:bg-primary transition-all text-white backdrop-blur-sm border border-white/10 group-hover:border-primary/50">
                <span class="material-symbols-outlined">play_arrow</span>
            </a>
        </div>
    </div>
</div>
