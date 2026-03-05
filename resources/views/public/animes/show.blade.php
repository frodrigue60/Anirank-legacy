@extends('layouts.app')

@section('title', $anime->title . ' - Themes List')
@section('description', 'Explore and listen to the openings and endings of ' . $anime->title . '.')
@section('og_image', $anime->cover_url)
@section('og_type', 'article')

@section('content')
    <style type="text/css">
        .sidebar-item {
            border-left: 2px solid transparent;
            padding-left: 0.75rem;
            transition: all 0.2s;
        }

        .sidebar-item:hover {
            border-left-color: #7f13ec;
        }

        .table-row-hover:hover {
            background: rgba(127, 19, 236, 0.05);
        }
    </style>

    <div class="max-w-[1440px] mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Sidebar --}}
            <aside class="lg:col-span-3 space-y-8">
                <div class="relative rounded-xl overflow-hidden shadow-2xl shadow-primary/10 border border-white/5 group">
                    <img src="{{ $anime->cover_url }}" alt="{{ $anime->title }}"
                        class="w-full h-auto aspect-2/3 object-cover transition-transform duration-700 group-hover:scale-105">
                    <div
                        class="absolute top-0 left-0 w-full h-full bg-linear-to-t from-background-dark/80 via-transparent to-transparent opacity-60">
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border-b border-primary/20 pb-2">
                        <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Information</h3>
                    </div>
                    <div class="space-y-5 text-sm">
                        @isset($anime->format)
                            <div class="sidebar-item">
                                <span class="block text-xs text-white/40 mb-0.5">Format</span>
                                <span class="font-medium text-white">{{ $anime->format->name }}</span>
                            </div>
                        @endisset

                        <div class="sidebar-item">
                            <span class="block text-xs text-white/40 mb-0.5">Release</span>
                            <span class="font-medium text-white">
                                {{ $anime->season->name ?? '' }} {{ $anime->year->name ?? '' }}
                            </span>
                        </div>

                        @if ($anime->studios->count() > 0)
                            <div class="sidebar-item">
                                <span class="block text-xs text-white/40 mb-0.5">Studios</span>
                                <div class="flex flex-col gap-1">
                                    @foreach ($anime->studios as $item)
                                        <a href="{{ route('studios.show', $item) }}"
                                            class="font-medium text-primary hover:underline">
                                            {{ $item->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($anime->producers->count() > 0)
                            <div class="sidebar-item">
                                <span class="block text-xs text-white/40 mb-0.5">Producers</span>
                                <div class="flex flex-col gap-1">
                                    @foreach ($anime->producers as $item)
                                        <a href="{{ route('producers.show', $item) }}"
                                            class="font-medium text-primary hover:underline">
                                            {{ $item->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($anime->externalLinks->count() > 0)
                    <div class="space-y-4 pt-6 border-t border-white/5">
                        <h3 class="text-xs font-black text-primary uppercase tracking-[0.2em]">External Links</h3>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($anime->externalLinks as $item)
                                @php
                                    $linkName = strtolower($item->name);
                                    $colorClass = match (true) {
                                        str_contains($linkName, 'official')
                                            => 'text-emerald-400 border-emerald-500/20 bg-emerald-500/5 hover:border-emerald-500/50',
                                        str_contains($linkName, 'crunchy')
                                            => 'text-orange-400 border-orange-500/20 bg-orange-500/5 hover:border-orange-500/50',
                                        str_contains($linkName, 'mal')
                                            => 'text-blue-400 border-blue-500/20 bg-blue-500/5 hover:border-blue-500/50',
                                        str_contains($linkName, 'twitter') || str_contains($linkName, ' x ')
                                            => 'text-sky-400 border-sky-500/20 bg-sky-500/5 hover:border-sky-500/50',
                                        str_contains($linkName, 'netflix')
                                            => 'text-red-500 border-red-500/20 bg-red-500/5 hover:border-red-500/50',
                                        str_contains($linkName, 'hidive')
                                            => 'text-blue-300 border-blue-400/20 bg-blue-400/5 hover:border-blue-400/50',
                                        default
                                            => 'text-white/60 border-white/10 bg-white/5 hover:border-primary/50 hover:text-white',
                                    };
                                @endphp
                                <a href="{{ $item->url }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border {{ $colorClass }} transition-all text-[11px] font-bold group/link">
                                    <span
                                        class="material-symbols-outlined text-base opacity-60 group-hover/link:opacity-100">link</span>
                                    {{ $item->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            {{-- Main Content --}}
            <section class="lg:col-span-9 space-y-10">
                <div class="flex flex-col gap-6 border-b border-primary/20 pb-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            @auth
                                @if (Auth::User()->isStaff())
                                    <div class="flex items-center gap-2 mb-4">
                                        <a href="{{ route('admin.animes.edit', $anime->id) }}"
                                            class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded text-xs font-bold hover:bg-emerald-500/20 transition-all">EDIT</a>
                                        <a href="{{ route('admin.songs.index', ['anime_id' => $anime->id]) }}"
                                            class="px-3 py-1 bg-primary/10 text-primary border border-primary/20 rounded text-xs font-bold hover:bg-primary/20 transition-all">SONGS</a>
                                    </div>
                                @endif
                            @endauth
                            <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white mb-2">
                                {{ $anime->title }}
                            </h1>
                            <h2 class="text-lg md:text-xl text-white/40 font-medium">
                                {{ $anime->native_title ?? '' }}
                            </h2>
                        </div>
                    </div>

                    @if ($anime->genres->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($anime->genres as $genre)
                                <span
                                    class="px-3 py-1 rounded border border-white/10 text-white/60 text-xs font-medium bg-surface-dark hover:border-primary/30 hover:text-primary transition-all cursor-default">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Synopsis --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-bold flex items-center gap-3 text-white">
                        <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                        Synopsis
                    </h2>
                    <div class="glass-panel p-8 rounded-2xl" x-data="{ expanded: false }">
                        <div class="text-white/80 leading-relaxed text-lg font-body prose prose-invert max-w-none transition-all duration-300"
                            :class="expanded ? '' : 'line-clamp-4'">
                            {!! $anime->description !!}
                        </div>
                        <button @click="expanded = !expanded"
                            class="mt-4 text-primary font-bold text-sm hover:text-white transition-colors flex items-center gap-1 group">
                            <span x-text="expanded ? 'Read Less' : 'Read More'"></span>
                            <span class="material-symbols-outlined text-sm transition-transform duration-300"
                                :class="expanded ? 'rotate-180' : ''">expand_more</span>
                        </button>
                    </div>
                </div>

                {{-- Music Themes --}}
                <div class="space-y-6 pt-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold flex items-center gap-3 text-white">
                            <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                            Music Themes
                        </h2>
                    </div>

                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/5 text-xs uppercase tracking-widest text-white/30">
                                    <th class="p-5 font-bold w-24">Type</th>
                                    <th class="p-5 font-bold">Song Title</th>
                                    <th class="p-5 font-bold">Artist</th>
                                    <th class="p-5 font-bold text-right">Avg Rating</th>
                                    <th class="p-5 font-bold w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                {{-- Openings --}}
                                @foreach ($openings->sortBy('theme_num') as $song)
                                    <tr class="table-row-hover border-b border-white/5 group transition-colors">
                                        <td class="p-5">
                                            <span
                                                class="inline-flex items-center justify-center px-2.5 py-1 rounded bg-green-500/10 text-green-400 border border-green-500/20 text-[10px] font-bold uppercase">
                                                OP {{ $song->theme_num }}
                                            </span>
                                        </td>
                                        <td class="p-5">
                                            <div class="font-bold text-white text-base">{{ $song->name }}</div>

                                        </td>
                                        <td class="p-5 text-white/70">
                                            @foreach ($song->artists as $artist)
                                                <a href="{{ route('artists.show', $artist) }}"
                                                    class="hover:text-primary transition-colors">
                                                    {{ $artist->name }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        </td>
                                        <td class="p-5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <span class="material-symbols-outlined text-yellow-400 text-sm"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-bold text-white text-lg">{{ number_format($song->avg_rating, 1) }}</span>
                                            </div>
                                        </td>
                                        <td class="p-5 text-right">
                                            <a href="{{ route('songs.show.nested', [$anime, $song]) }}"
                                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white/40 hover:bg-primary hover:text-white transition-colors">
                                                <span class="material-symbols-outlined text-lg">play_arrow</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Endings --}}
                                @foreach ($endings->sortBy('theme_num') as $song)
                                    <tr
                                        class="table-row-hover @if (!$loop->last) border-b border-white/5 @endif group transition-colors">
                                        <td class="p-5">
                                            <span
                                                class="inline-flex items-center justify-center px-2.5 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold uppercase">
                                                ED {{ $song->theme_num }}
                                            </span>
                                        </td>
                                        <td class="p-5">
                                            <div class="font-bold text-white text-base">{{ $song->name }}</div>
                                        </td>
                                        <td class="p-5 text-white/70">
                                            @foreach ($song->artists as $artist)
                                                <a href="{{ route('artists.show', $artist) }}"
                                                    class="hover:text-primary transition-colors">
                                                    {{ $artist->name }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        </td>
                                        <td class="p-5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <span class="material-symbols-outlined text-yellow-400 text-sm"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-bold text-white text-lg">{{ number_format($song->avg_rating, 1) }}</span>
                                            </div>
                                        </td>
                                        <td class="p-5 text-right">
                                            <a href="{{ route('songs.show.nested', [$anime, $song]) }}"
                                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white/40 hover:bg-primary hover:text-white transition-colors">
                                                <span class="material-symbols-outlined text-lg">play_arrow</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection


