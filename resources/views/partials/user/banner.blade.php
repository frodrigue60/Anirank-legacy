@php
    $profileUrl = $user->avatar_url;
    $bannerUrl = $user->banner_url;
@endphp

<div class="relative w-full overflow-hidden">
    {{-- Banner Image with Gradient Overlay --}}
    <div class="h-[250px] md:h-[350px] w-full bg-surface-dark relative bg-cover bg-center transition-all duration-700 group/banner"
        style="background-image: url('{{ $bannerUrl }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent"></div>
        <div class="absolute inset-0 bg-black/20 group-hover/banner:bg-black/10 transition-colors duration-500"></div>
    </div>

    {{-- Content Area --}}
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 relative -mt-20 md:-mt-24 pb-4">
        <div class="flex flex-col md:flex-row items-center md:items-end gap-6 md:gap-8">
            {{-- Avatar Wrapper --}}
            <div class="relative group/avatar">
                <div
                    class="absolute -inset-1 bg-gradient-to-tr from-primary to-primary-light rounded-full blur opacity-25 group-hover/avatar:opacity-50 transition duration-500">
                </div>
                <div
                    class="relative w-32 h-32 md:w-44 md:h-44 rounded-full border-4 border-background overflow-hidden bg-surface-dark shadow-2xl">
                    <img src="{{ $profileUrl }}" alt="{{ $user->name }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110">
                </div>
            </div>

            {{-- User Info --}}
            <div class="flex-1 text-center md:text-left pb-4 md:pb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex flex-col gap-3 md:gap-4 mt-1">
                            <div class="flex items-center justify-center md:justify-start gap-4">
                                <h1 class="text-3xl md:text-5xl font-black tracking-tight text-white drop-shadow-xl">
                                    {{ $user->name }}
                                </h1>
                                {{-- Level Badge --}}
                                <div class="bg-primary/20 backdrop-blur-md border border-primary/30 px-3 py-1 rounded-full flex items-center gap-2 shadow-lg shadow-primary/10 group/level hover:bg-primary/30 transition-all duration-300">
                                    <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Lv.</span>
                                    <span class="text-lg font-black text-white leading-none">{{ $user->level }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-[0.15em]">
                                    <span class="text-white/40">{{ $user->level_name }}</span>
                                    <span class="text-primary">{{ number_format($user->xp) }} <span class="text-white/20">/</span> {{ $user->next_level ? number_format($user->next_level->min_xp) : 'MAX' }} XP</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full bg-gradient-to-r from-primary to-primary-light transition-all duration-1000 shadow-[0_0_12px_rgba(127,19,236,0.5)]"
                                        style="width: {{ $user->xp_progress }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center md:justify-start gap-3">
                                <div class="h-1 w-12 bg-primary rounded-full"></div>
                                <span class="text-white/40 text-[10px] font-black uppercase tracking-[0.2em]">Collector
                                    Archive</span>
                            </div>
                        </div>

                        {{-- Stats & Badges --}}
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 mt-6">
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col">
                                    <span class="text-white font-black text-lg md:text-xl">{{ $user->followers()->count() }}</span>
                                    <span class="text-white/40 text-[10px] md:text-xs font-bold uppercase tracking-widest">Followers</span>
                                </div>
                                <div class="h-8 w-px bg-white/10"></div>
                                <div class="flex flex-col">
                                    <span class="text-white font-black text-lg md:text-xl">{{ $user->following()->count() }}</span>
                                    <span class="text-white/40 text-[10px] md:text-xs font-bold uppercase tracking-widest">Following</span>
                                </div>
                            </div>

                            @if($user->badges->count() > 0)
                                <div class="h-8 w-px bg-white/10 hidden md:block"></div>
                                <div class="flex items-center gap-2">
                                    @foreach($user->badges as $badge)
                                        <div class="w-10 h-10 rounded-xl bg-surface-dark border border-white/5 p-1.5 group/badge relative hover:scale-110 transition-transform duration-300 cursor-help"
                                            title="{{ $badge->name }}: {{ $badge->description }}">
                                            <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover/badge:opacity-100 transition-opacity rounded-xl"></div>
                                            @if($badge->icon_url)
                                                <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}" class="w-full h-full object-contain filter drop-shadow-sm">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-primary text-[20px] filled">military_tech</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if(auth()->check() && auth()->id() !== $user->id)
                        <div class="flex items-center justify-center md:justify-end">
                            @livewire('follow-button', ['user' => $user])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


