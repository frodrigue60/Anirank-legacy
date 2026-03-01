@extends('layouts.app')

@section('title', 'Ranking Anime Openings & Endings | ' . config('app.name'))

@section('description',
    'The site you were looking for to rate openings and endings of your favorite animes. Discover
    which are the most popular opening and endings.')

@section('content')
    @guest
        <section class="relative h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-linear-to-t from-background-dark via-background-dark/60 to-transparent z-10">
                </div>
                <div class="absolute inset-0 bg-linear-to-r from-background-dark/80 via-transparent to-background-dark/80 z-10">
                </div>
                <img class="w-full h-full object-cover scale-105" data-alt="Cinematic wide shot of anime cityscape at night"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4NKMsiVFSxKoMy0QJq1bdQInEoybDwBon5RstY0kyN604vExTI9pu8NcCmoEH1v6KITdZ4av_LtDghv2w-_XaKXQElk4leiUH9GVq1u0IyulYjmeCIqspuNtkNr8PCg2bZhyubZmFlVu3i7A2-Ug2FVNBcK4uF7HEtkwYOOHS-swOGUyTo14YgLkUPJsdetP6SpSqK0fw-RR74Zhd-zOxh-7r1ruGpFQDSftrvuYB_oXOcfl7wzCHPUY-joO8Cn9Nr-6SuEVYS6A" />
            </div>
            <div class="relative z-20 container mx-auto px-6 text-center max-w-4xl">
                <h1
                    class="text-5xl md:text-7xl font-black mb-6 tracking-tight leading-none text-transparent bg-clip-text bg-linear-to-b from-white to-white/60">
                    Your Anime Soundtrack Journey Starts Here
                </h1>
                <div class="flex flex-wrap justify-center gap-8 mb-10 text-lg">
                    <div class="flex items-center gap-2 text-white/90">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        <span>Track favorites</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <span class="material-symbols-outlined text-primary">stars</span>
                        <span>Influence rankings</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <span class="material-symbols-outlined text-primary">playlist_add_check</span>
                        <span>Curate playlists</span>
                    </div>
                </div>
                <a href="{{ route('songs.index') }}"
                    class="group relative inline-flex items-center gap-3 bg-primary px-10 py-5 rounded-xl font-bold text-xl transition-all hover:scale-105 active:scale-95 neon-glow">
                    <span>Start Exploring</span>
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce opacity-50">
                <span class="material-symbols-outlined text-3xl">keyboard_double_arrow_down</span>
            </div>
        </section>
    @endguest
    <main class="flex-1 w-full max-w-[1440px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 px-6 py-8">
        <div class="lg:col-span-9 flex flex-col gap-10">
            {{-- FEATURED THEME --}}
            @isset($featuredSong)
                @php
                    $user = auth()->user();
                    $format = $user?->score_format ?? 'POINT_100';
                    $score = $featuredSong->formattedAvgScore($format);
                @endphp
                <section class="relative w-full rounded-2xl overflow-hidden bg-surface-dark group">
                    <div class="absolute inset-0 bg-cover bg-center opacity-60 mix-blend-overlay transition-transform duration-700 group-hover:scale-105"
                        style="background-image: url('{{ $featuredSong->anime->banner_url }}');">
                    </div>
                    <div class="absolute inset-0 bg-linear-to-r from-background-dark via-background-dark/80 to-transparent">
                    </div>
                    <div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row gap-8 items-center md:items-end">
                        <div class="relative shrink-0 hero-glow">
                            <div
                                class="w-48 h-48 md:w-64 md:h-64 rounded-xl shadow-2xl overflow-hidden relative border-2 border-white/10">
                                <x-ui.image :src="$featuredSong->anime->thumbnail_url" :alt="$featuredSong->name" class="w-full h-full object-cover" />
                                <div class="absolute bottom-0 inset-x-0 bg-linear-to-t from-black/80 to-transparent p-4 pt-10">
                                    <div class="flex items-center gap-1 text-yellow-400 font-bold text-lg">
                                        <span class="material-symbols-outlined filled">star</span>
                                        <span>{{ $score }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 text-center md:text-left flex-1">
                            <div
                                class="inline-flex items-center gap-2 self-center md:self-start bg-white/5 border border-white/10 px-3 py-1 rounded-full backdrop-blur-md">
                                <span class="material-symbols-outlined text-primary text-sm filled">auto_awesome</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-white/90">Featured Theme</span>
                            </div>
                            <div>
                                <h1
                                    class="text-xl md:text-2xl font-black leading-tight tracking-tight text-white drop-shadow-xl mb-2 truncate">
                                    {{ $featuredSong->name }}</h1>
                                <div
                                    class="flex flex-col md:flex-col items-center md:items-start gap-1 md:gap-3 text-md md:text-lg text-white/80">
                                    <span class="font-bold text-primary truncate">{{ $featuredSong->anime->title }}</span>
                                    <span class="font-medium text-white truncate">
                                        @foreach ($featuredSong->artists as $artist)
                                            {{ $artist->name }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mt-2 justify-center md:justify-start">
                                <a href="{{ route('songs.show.nested', ['anime' => $featuredSong->anime->slug, 'song' => $featuredSong->slug]) }}"
                                    class="bg-primary hover:bg-primary/80 text-white h-12 px-8 rounded-full font-bold flex items-center gap-2 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-primary/30">
                                    <span class="material-symbols-outlined filled">play_circle</span>
                                    <span>Play Now</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            @endisset

            {{-- WEEKLY RANKINGS --}}
            <section>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 px-1 gap-4">
                    <h2 class="text-2xl font-bold tracking-tight flex items-center gap-2 text-white">
                        <span class="material-symbols-outlined text-primary">leaderboard</span>
                        Weekly Rankings
                    </h2>
                    <a href="{{ route('songs.ranking') }}"
                        class="flex items-center gap-1.5 text-primary text-xs font-bold uppercase tracking-wide hover:underline transition-colors">
                        View Full Ranking
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($weaklyRanking->chunk(ceil($weaklyRanking->count() / 2)) as $chunk)
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white/50 text-xs font-bold uppercase tracking-wider">Top
                                    {{ $loop->first ? 'Openings' : 'Endings' }}</span>
                            </div>
                            @foreach ($chunk as $song)
                                @php
                                    $rankNum = $loop->iteration;
                                    $isTopThree = $rankNum <= 3;
                                    $medalColors = [
                                        1 => 'border-primary/50 bg-primary/10',
                                        2 => 'border-primary/30 bg-primary/5',
                                        3 => 'border-primary/20 bg-primary/[0.03]',
                                    ];
                                    $badgeColors = [
                                        1 => 'bg-primary text-white',
                                        2 => 'bg-primary/70 text-white',
                                        3 => 'bg-primary/50 text-white',
                                    ];
                                    $cardClass = $isTopThree
                                        ? $medalColors[$rankNum] ?? ''
                                        : 'border-white/5 bg-surface-darker';
                                    $badgeClass = $isTopThree
                                        ? $badgeColors[$rankNum] ?? 'bg-surface-dark text-white'
                                        : 'bg-surface-dark text-white';

                                    $user = auth()->user();
                                    $format = $user?->score_format ?? 'POINT_100';
                                    $score = $song->formattedAvgScore($format);
                                @endphp
                                <div
                                    class="group relative p-3 rounded-xl hover:bg-surface-dark transition-colors border flex gap-4 items-center {{ $cardClass }}">
                                    <div class="relative w-20 h-20 shrink-0 rounded-lg overflow-hidden">
                                        <x-ui.image :src="$song->anime->thumbnail_url" :alt="$song->name"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                        <div
                                            class="absolute top-1 left-1 {{ $badgeClass }} text-xs font-bold px-1.5 py-0.5 rounded shadow">
                                            #{{ $rankNum }}</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <a href="{{ route('songs.show.nested', ['anime' => $song->anime->slug, 'song' => $song->slug]) }}"
                                                class="font-bold text-white truncate text-lg hover:text-primary transition-colors">{{ $song->name }}</a>
                                            <div
                                                class="flex items-center gap-1 bg-surface-dark/80 text-yellow-400 text-xs font-bold px-2 py-1 rounded-lg border border-white/5 shrink-0">
                                                <span class="material-symbols-outlined filled text-sm">star</span>
                                                <span>{{ $score }}</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-primary font-medium truncate">{{ $song->anime->title }}</p>
                                        <p class="text-xs text-white/50 truncate">
                                            @foreach ($song->artists as $artist)
                                                @if ($artist->slug)
                                                    <a href="{{ route('artists.show', $artist->slug) }}"
                                                        class="hover:text-primary transition-colors">{{ $artist->name }}</a>{{ !$loop->last ? ', ' : '' }}
                                                @else
                                                    {{ $artist->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- TABS SECTION --}}
            <section class="pb-12">
                <div class="border-b border-white/5 mb-6 flex justify-between items-center">
                    <div class="flex gap-8 items-center" id="tabs">
                        <button id="recently-tab"
                            class="py-4 border-b-2 border-primary text-white font-bold text-sm tracking-wide transition-all active-tab-link leading-none">Recently
                            Added</button>
                        <button id="popular-tab"
                            class="py-4 border-b-2 border-transparent text-white/40 hover:text-white font-medium text-sm tracking-wide transition-colors leading-none">Most
                            Popular</button>
                        <button id="viewed-tab"
                            class="py-4 border-b-2 border-transparent text-white/40 hover:text-white font-medium text-sm tracking-wide transition-colors leading-none">Most
                            Viewed</button>
                    </div>
                    <div class="flex items-center">
                        <button
                            class="prev-tab border border-white/5 text-white/50 hover:text-white text-md h-9 w-9 m-1 flex items-center justify-center rounded-lg transition-colors bg-white/5"><span
                                class="material-symbols-outlined text-xl">chevron_left</span></button>
                        <button
                            class="next-tab border border-white/5 text-white/50 hover:text-white text-md h-9 w-9 m-1 flex items-center justify-center rounded-lg transition-colors bg-white/5"><span
                                class="material-symbols-outlined text-xl">chevron_right</span></button>
                    </div>
                </div>
                <div id="tab-content">
                    <div id="recently" class="tab-pane">
                        <div class="custom-carousel-wrapper relative group/carousel">
                            <div
                                class="custom-carousel flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth py-2 px-1 pb-4">
                                @include('partials.songs.cards-carousel', ['songs' => $recently])
                            </div>
                        </div>
                    </div>
                    <div id="popular" class="tab-pane hidden">
                        <div class="custom-carousel-wrapper relative group/carousel">
                            <div
                                class="custom-carousel flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth py-2 px-1 pb-4">
                                @include('partials.songs.cards-carousel', ['songs' => $popular])
                            </div>
                        </div>
                    </div>
                    <div id="viewed" class="tab-pane hidden">
                        <div class="custom-carousel-wrapper relative group/carousel">
                            <div
                                class="custom-carousel flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth py-2 px-1 pb-4">
                                @include('partials.songs.cards-carousel', ['songs' => $viewed])
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {{-- LIVE ACTIVITY FEED --}}
            <livewire:activity-feed />
        </div>

        {{-- SIDEBAR --}}
        <aside class="lg:col-span-3 flex flex-col gap-8 sm:max-w-[400px] sm:mx-auto">
            <div class="bg-surface-darker rounded-2xl p-6 border border-white/5">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-white text-lg">Featured Artists</h3>
                    <a href="{{ route('artists.index') }}"
                        class="text-primary text-xs font-bold uppercase tracking-wide hover:underline">View All</a>
                </div>
                <div class="flex flex-col gap-5">
                    @foreach ($artists->take(5) as $artist)
                        <div class="flex items-center justify-between group">
                            <a href="{{ route('artists.show', $artist->slug) }}" class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden border-2 border-transparent group-hover:border-primary transition-colors shrink-0">
                                    @if ($artist->images()->where('type', 'thumbnail')->exists())
                                        <x-ui.image :src="$artist->thumbnail_url" :alt="$artist->name"
                                            class="w-full h-full object-cover" />
                                    @else
                                        <div
                                            class="w-full h-full bg-primary/20 flex items-center justify-center text-primary">
                                            <span class="material-symbols-outlined">person</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4
                                        class="text-sm font-bold text-white group-hover:text-primary transition-colors truncate">
                                        {{ $artist->name }}</h4>
                                    <p class="text-xs text-white/40 truncate">{{ $artist->songs_count ?? '0' }} Themes</p>
                                </div>
                            </a>
                            <a href="{{ route('artists.show', $artist->slug) }}"
                                class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-primary transition-colors">
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- EVENT/COMMUNITY BOX --}}
            <div class="relative rounded-2xl overflow-hidden aspect-4/5 flex flex-col justify-end p-6 group">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAH_og0-9n8HCwoymOtvxCZ2iSwhi1s0A9GouQSn-K7NzkZ13nzTIqMOJ6cq1utUw0s-FL7CewN6C8VFcJizGXn7mHZASt9HtY3-Lhm7ktTQLi7ouj5QP8adxWImQ_5RxcUGZRHyujm7HGEcipxIes_YOw3FD7C2XnhIYlfxnYJ8zeWxbouotxhLpbP4NwK2OAuftUr0xAL6mD6jN8392YwYPYusIbgoJXxmVZ38rnjbBuYc5uaDeXAuuWdDfBjH2c72v2y7Btneg8');">
                </div>
                <div class="absolute inset-0 bg-linear-to-t from-black via-black/50 to-transparent"></div>
                <div class="relative z-10 text-white">
                    <span
                        class="bg-[#5865F2] text-white text-[10px] font-bold uppercase px-2 py-1 rounded mb-2 inline-block">Discord</span>
                    <h3 class="text-2xl font-bold mb-2 leading-tight">Join Our Community</h3>
                    <p class="text-white/70 text-sm mb-4">Discuss your favorite themes with other fans!</p>
                    <a href="#"
                        class="w-full bg-[#5865F2] text-white font-bold py-3 rounded-lg hover:bg-[#4752C4] transition-colors inline-block text-center">Join
                        Now</a>
                </div>
            </div>

            {{-- ADS placeholder --}}
            <div
                class="p-8 rounded-2xl text-center border border-dashed border-white/10 bg-surface-darker/50 flex flex-col items-center justify-center min-h-[250px] group">
                <span class="material-symbols-outlined text-[32px] text-white/10 mb-2">ad_units</span>
                <div class="text-white/20 text-[10px] font-black tracking-[0.2em] uppercase">Advertisement</div>
            </div>
        </aside>
    </main>

@endsection

@push('scripts')
    <style>
        .custom-carousel {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .custom-carousel::-webkit-scrollbar {
            display: none;
        }

        .custom-carousel>* {
            snap-align: start;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switching Logic
            const tabs = document.querySelectorAll('#tabs button');
            const panes = document.querySelectorAll('#tab-content .tab-pane');

            function setActiveTab(clickedTab) {
                tabs.forEach(tab => {
                    tab.classList.remove('border-primary', 'text-white', 'font-bold', 'active-tab-link');
                    tab.classList.add('border-transparent', 'text-white/40', 'font-medium');
                });

                clickedTab.classList.add('border-primary', 'text-white', 'font-bold', 'active-tab-link');
                clickedTab.classList.remove('border-transparent', 'text-white/40', 'font-medium');

                const targetId = clickedTab.id.replace('-tab', '');
                panes.forEach(pane => {
                    if (pane.id === targetId) {
                        pane.classList.remove('hidden');
                    } else {
                        pane.classList.add('hidden');
                    }
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    setActiveTab(tab);
                });
            });

            // Carousel Navigation Logic
            const prevBtns = document.querySelectorAll('.prev-tab');
            const nextBtns = document.querySelectorAll('.next-tab');

            function getActiveCarousel() {
                const activePane = document.querySelector('#tab-content .tab-pane:not(.hidden)');
                return activePane ? activePane.querySelector('.custom-carousel') : null;
            }

            prevBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const carousel = getActiveCarousel();
                    if (carousel) {
                        const scrollAmount = carousel.offsetWidth * 0.8;
                        carousel.scrollBy({
                            left: -scrollAmount,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            nextBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const carousel = getActiveCarousel();
                    if (carousel) {
                        const scrollAmount = carousel.offsetWidth * 0.8;
                        carousel.scrollBy({
                            left: scrollAmount,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
@endpush
