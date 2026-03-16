@extends('layouts.admin')

@section('title', 'Add Song')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add Theme Song</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">
                @if ($currentAnime)
                    Register a new opening or ending for {{ $currentAnime->title }}
                @else
                    Register a new opening or ending entry
                @endif
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8"
            x-data="songForm()">
            <form method="post" action="{{ route('admin.songs.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{--  POST  --}}
                <div>
                    <label for="post_search"
                        class="block text-sm font-bold text-zinc-400 uppercase tracking-widest mb-2">Anime</label>
                    <div class="relative">
                        <input type="hidden" name="anime_id" :value="selectedId" id="anime_id">

                        {{-- Search Input / Selected View --}}
                        <div class="relative group">
                            <input type="text" id="post_search" x-model="search" x-on:input.debounce.300ms="fetchPosts()"
                                x-on:focus="showResults = true" :placeholder="selectedTitle ? '' : 'Search anime...'"
                                :readonly="selectedId !== null"
                                :class="selectedId ? 'bg-blue-600/20 border-blue-500/50 text-blue-100 font-bold pr-12' :
                                    'bg-zinc-950/50 border-zinc-800 text-white'"
                                class="block w-full border rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 outline-none">

                            {{-- Selected State Icon & Clear Button --}}
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                <template x-if="selectedId">
                                    <button type="button" @click="clearSelection()"
                                        class="text-zinc-500 hover:text-white transition-colors">
                                        <span class="material-symbols-outlined text-xl">close</span>
                                    </button>
                                </template>
                                <template x-if="!selectedId">
                                    <span
                                        class="material-symbols-outlined text-zinc-600 group-focus-within:text-blue-500 transition-colors">search</span>
                                </template>
                            </div>

                            {{-- Selected Title Overlay (Better UX than just value) --}}
                            <template x-if="selectedId">
                                <div class="absolute inset-0 flex items-center px-4 pointer-events-none">
                                    <span x-text="selectedTitle"
                                        class="text-sm font-bold truncate pr-16 bg-transparent"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Results Dropdown --}}
                        <div x-show="showResults && results.length > 0" x-cloak @click.away="showResults = false"
                            class="absolute z-50 w-full mt-2 bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl">
                            <div class="max-h-60 overflow-y-auto">
                                <template x-for="post in results" :key="post.id">
                                    <button type="button" @click="selectPost(post)"
                                        class="w-full text-left px-4 py-3 hover:bg-blue-600/20 hover:text-blue-400 transition-all flex items-center gap-3 border-b border-zinc-800/50 last:border-0">
                                        <span class="material-symbols-outlined text-zinc-500">movie</span>
                                        <div class="flex flex-col">
                                            <span x-text="post.title" class="text-sm font-medium"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Loading State --}}
                        <div x-show="loading" class="absolute right-12 top-1/2 -translate-y-1/2">
                            <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full">
                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        function songForm() {
                            return {
                                search: '',
                                results: [],
                                selectedId: {{ old('anime_id') ?? $selectedAnimeId ? old('anime_id') ?? $selectedAnimeId : 'null' }},
                                selectedTitle: '{{ $currentAnime ? addslashes($currentAnime->title) : '' }}',
                                loading: false,
                                showResults: false,

                                // Theme numbering
                                type: '{{ old('type', 'OP') }}',
                                themeNum: '{{ old('theme_num') }}',

                                init() {
                                    this.$watch('selectedId', (value) => {
                                        if (value) this.fetchSuggestedNumber();
                                    });
                                    this.$watch('type', () => this.fetchSuggestedNumber());

                                    // Initial load if post is pre-selected
                                    if (this.selectedId && !this.themeNum) {
                                        this.fetchSuggestedNumber();
                                    }
                                },

                                fetchSuggestedNumber() {
                                    if (!this.selectedId || !this.type) return;

                                    fetch(`{{ route('admin.songs.latest_number') }}?anime_id=${this.selectedId}&type=${this.type}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.themeNum = data.next_number;
                                        })
                                        .catch(err => console.error('Error fetching latest number:', err));
                                },

                                fetchPosts() {
                                    if (this.search.length < 2) {
                                        this.results = [];
                                        return;
                                    }

                                    this.loading = true;
                                    fetch(`{{ route('admin.animes.autocomplete') }}?q=${encodeURIComponent(this.search)}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.results = data;
                                            this.loading = false;
                                            this.showResults = true;
                                        })
                                        .catch(err => {
                                            console.error('Error fetching posts:', err);
                                            this.loading = false;
                                        });
                                },

                                selectPost(post) {
                                    this.selectedId = post.id;
                                    this.selectedTitle = post.title;
                                    this.search = ''; // Clear search text
                                    this.results = [];
                                    this.showResults = false;
                                },

                                clearSelection() {
                                    this.selectedId = null;
                                    this.selectedTitle = '';
                                    this.search = '';
                                    this.results = [];
                                    this.themeNum = '';
                                    this.$nextTick(() => document.getElementById('post_search').focus());
                                }
                            }
                        }
                    </script>
                @endpush

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- OP/ED Number --}}
                    <div class="space-y-2">
                        <label for="theme_num" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">OP/ED
                            Number</label>
                        <input type="number" name="theme_num" id="theme_num" x-model="themeNum"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                            placeholder="e.g. 1">
                    </div>

                    {{-- Type --}}
                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Theme
                            Type</label>
                        <select name="type" id="type" x-model="type"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                            @foreach ($types as $item)
                                <option value="{{ $item['value'] }}">
                                    {{ $item['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Song Names --}}
                <div class="space-y-6 bg-zinc-950/30 p-6 rounded-2xl border border-zinc-800/50">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2">music_note</span> SONG METADATA
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label for="songRomaji" class="block text-sm font-bold text-zinc-400">Song Name (Romaji)</label>
                            <input type="text" name="song_romaji" id="songRomaji" value="{{ old('song_romaji') }}"
                                class="block w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                                placeholder="Enter romaji title...">
                        </div>

                        <div class="space-y-2">
                            <label for="songJp" class="block text-sm font-bold text-zinc-400">Song Name (Japanese)</label>
                            <input type="text" name="song_jp" id="songJp" value="{{ old('song_jp') }}"
                                class="block w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                                placeholder="Enter original Japanese title...">
                        </div>

                        <div class="space-y-2">
                            <label for="songEn" class="block text-sm font-bold text-zinc-400">Song Name (English)</label>
                            <input type="text" name="song_en" id="songEn" value="{{ old('song_en') }}"
                                class="block w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                                placeholder="Enter localized English title...">
                        </div>
                    </div>
                </div>

                {{-- Artists --}}
                <div class="space-y-2">
                    <label for="artists-input"
                        class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Artists</label>
                    <input type="text" name="artists" id="artists-input" value="{{ old('artists') }}" required
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                        placeholder="Artist 1, Artist 2, ...">
                    <p class="text-[10px] text-zinc-500 mt-1 italic">Please separate multiple artists with commas.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Season --}}
                    <div class="space-y-2">
                        <label for="season_id"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Season</label>
                        <select name="season_id" id="season_id"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                            @foreach ($seasons as $season)
                                <option value="{{ $season->id }}"
                                    {{ old('season_id', $currentPost->season_id ?? '') == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Year --}}
                    <div class="space-y-2">
                        <label for="year_id"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Year</label>
                        <select name="year_id" id="year_id"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}"
                                    {{ old('year_id', $currentPost->year_id ?? '') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Trigger --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">cloud_upload</span>
                        SAVE SONG ENTRY
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
