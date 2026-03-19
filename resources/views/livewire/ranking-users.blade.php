@section('title', 'Ranking de Usuarios - Anirank')
@section('description', 'Ranking de los mejores usuarios de Anirank basado en su XP, nivel y actividad.')

<div x-data="{}" class="max-w-[1440px] mx-auto px-6 md:px-14 py-8 md:py-12">
    {{-- Header Section --}}
    <div class="flex flex-col gap-8 mb-12">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span
                        class="bg-primary/20 text-primary text-[10px] font-black px-2.5 py-1 rounded-full border border-primary/30 uppercase tracking-[0.2em]">Community
                        Leaderboard</span>
                </div>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black tracking-tighter text-white">
                    User Ranking
                </h1>
            </div>

            {{-- Filter Selector --}}
            <div class="flex items-center gap-4 bg-surface-dark/50 p-1.5 rounded-2xl border border-white/5"
                wire:loading.class="opacity-50 pointer-events-none transition-opacity">
                <button wire:click="setSort('xp')"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $sort === 'xp' ? 'bg-primary text-white shadow-lg shadow-primary/20 cursor-default' : 'text-white/40 hover:text-white/60' }}">
                    XP / Level
                </button>
                <button wire:click="setSort('ratings_count')"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $sort === 'ratings_count' ? 'bg-primary text-white shadow-lg shadow-primary/20 cursor-default' : 'text-white/40 hover:text-white/60' }}">
                    Ratings
                </button>
                <button wire:click="setSort('comments_count')"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $sort === 'comments_count' ? 'bg-primary text-white shadow-lg shadow-primary/20 cursor-default' : 'text-white/40 hover:text-white/60' }}">
                    Comments
                </button>
            </div>
        </div>
    </div>

    {{-- Leaderboard Section --}}
    <div class="bg-surface-dark/30 border border-white/5 rounded-3xl overflow-hidden mb-12 group/table">
        {{-- Table Header --}}
        <div
            class="hidden sm:grid grid-cols-12 gap-4 px-8 py-4 border-b border-white/5 text-[10px] font-black uppercase tracking-widest text-white/30 bg-surface-darker/50">
            <div class="col-span-1 text-center">Rank</div>
            <div class="col-span-5">User Info</div>
            <div class="col-span-2 text-center">Level / XP</div>
            <div class="col-span-2 text-center">Ratings</div>
            <div class="col-span-2 text-center">Comments</div>
        </div>

        {{-- Table Body --}}
        <div class="flex flex-col min-h-[500px]">
            @foreach ($users as $user)
                @php
                    $formattedRank = str_pad($user->rank, 2, '0', STR_PAD_LEFT);
                @endphp

                <div wire:key="user-rank-{{ $user->id }}"
                    class="ranking-row grid grid-cols-[48px_1fr] sm:grid-cols-12 gap-x-4 gap-y-3 sm:gap-4 px-4 sm:px-8 py-4 sm:py-5 items-center transition-colors border-b border-white/5 hover:bg-white/5 group">
                    {{-- Rank Column --}}
                    <div class="sm:col-span-1 flex items-center justify-center">
                        <span
                            class="text-xl sm:text-2xl font-black {{ $user->rank <= 3 ? 'text-primary' : 'text-white/90' }}">{{ $formattedRank }}</span>
                    </div>

                    {{-- User Info Column --}}
                    <div class="sm:col-span-5 flex items-center gap-4 sm:gap-6">
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden shrink-0 shadow-lg shadow-black/40 border border-white/10">
                            <x-ui.image :src="$user->avatar_url" :alt="$user->name"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                fallback="default-avatar.webp" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('users.show', $user) }}"
                                class="text-base sm:text-lg font-bold text-white truncate block leading-tight hover:text-primary transition-colors">{{ $user->name }}
                            </a>
                            <div class="flex items-center gap-2 mt-1">
                                @foreach($user->roles as $role)
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-white/5 text-white/40 uppercase tracking-tighter">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Mobile only metrics --}}
                            <div class="flex items-center gap-4 mt-3 sm:hidden">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase text-white/20 leading-none mb-1">XP</span>
                                    <span class="text-xs font-bold text-white leading-none">{{ number_format($user->xp ?? 0) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase text-white/20 leading-none mb-1">Ratings</span>
                                    <span class="text-xs font-bold text-white leading-none">{{ number_format($user->ratings_count) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase text-white/20 leading-none mb-1">Comments</span>
                                    <span class="text-xs font-bold text-white leading-none">{{ number_format($user->comments_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Level / XP Column (Desktop Only) --}}
                    <div class="hidden sm:block sm:col-span-2 text-center">
                        <div class="text-xl font-black text-white tracking-tight">
                            Lvl. {{ $user->level ?? 1 }}
                        </div>
                        <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest">
                            {{ number_format($user->xp ?? 0) }} XP
                        </div>
                    </div>

                    {{-- Ratings Column (Desktop Only) --}}
                    <div class="hidden sm:block sm:col-span-2 text-center font-black text-white text-lg">
                        {{ number_format($user->ratings_count) }}
                    </div>

                    {{-- Comments Column (Desktop Only) --}}
                    <div class="hidden sm:block sm:col-span-2 text-center font-black text-white text-lg">
                        {{ number_format($user->comments_count) }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Infinite Scroll Trigger --}}
        @if ($hasMorePages)
            <div x-intersect.once="$wire.loadMore()" wire:key="intersect-users-ranking-{{ $page }}"
                class="p-8 border-t border-white/5 bg-surface-darker/30 flex flex-col items-center gap-4">
                <div class="w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <span class="text-xs font-bold text-white/20 uppercase tracking-widest">Loading more users...</span>
            </div>
        @endif
    </div>
</div>
