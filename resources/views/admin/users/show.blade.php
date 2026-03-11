@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">User Details</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Profile overview for {{ $user->name }}</p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95">
                    <span class="material-symbols-outlined mr-2 text-sm">edit</span>
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all border border-zinc-700">
                    <span class="material-symbols-outlined mr-2 text-sm">arrow_back</span>
                    Back
                </a>
            </div>
        </div>

        {{-- Profile Header Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden shadow-xl relative">
            <div class="h-32 w-full relative">
                @if($user->banner_url)
                    <img src="{{ $user->banner_url }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-blue-900/50 to-purple-900/50"></div>
                @endif
            </div>
            
            <div class="px-8 pb-8 pt-0 flex flex-col sm:flex-row gap-6 items-start sm:items-end -mt-12 relative z-10">
                <div class="w-24 h-24 rounded-full border-4 border-zinc-900 bg-zinc-800 overflow-hidden shrink-0">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
                
                <div class="flex-1 space-y-1">
                    <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-zinc-400 font-mono text-xs">@<span>{{ $user->slug }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Personal Information --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6">
                <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center mb-4">
                    <span class="material-symbols-outlined mr-2 text-blue-500 text-base">person</span> Info
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] text-zinc-500 uppercase font-black tracking-widest mb-1">Email Address</span>
                        <div class="text-sm font-medium text-white break-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-zinc-500 text-sm">mail</span>
                            {{ $user->email }}
                        </div>
                    </div>
                    
                    <div>
                        <span class="block text-[10px] text-zinc-500 uppercase font-black tracking-widest mb-1">Joined Date</span>
                        <div class="text-sm font-medium text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-zinc-500 text-sm">calendar_today</span>
                            {{ $user->created_at ? $user->created_at->format('F j, Y') : 'Unknown' }}
                        </div>
                    </div>

                    @if($user->last_login_at)
                    <div>
                        <span class="block text-[10px] text-zinc-500 uppercase font-black tracking-widest mb-1">Last Login</span>
                        <div class="text-sm font-medium text-zinc-400 flex items-center gap-2">
                            <span class="material-symbols-outlined text-zinc-500 text-sm">schedule</span>
                            {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Roles --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6">
                <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center mb-4">
                    <span class="material-symbols-outlined mr-2 text-purple-500 text-base">admin_panel_settings</span> Roles
                </h3>
                
                <div class="flex flex-wrap gap-2">
                    @forelse ($user->roles as $role)
                        @php
                            $roleColor = match ($role->slug) {
                                'admin' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                'editor' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'creator' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                default => 'bg-zinc-800 text-zinc-400 border-zinc-700',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest border {{ $roleColor }}">
                            {{ $role->name }}
                        </span>
                    @empty
                        <span class="text-zinc-500 text-sm italic">User has no roles assigned.</span>
                    @endforelse
                </div>
            </div>

            {{-- Badges --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6">
                <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center mb-4">
                    <span class="material-symbols-outlined mr-2 text-yellow-500 text-base">workspace_premium</span> Badges
                </h3>
                
                <div class="grid grid-cols-3 gap-3">
                    @forelse ($user->badges as $badge)
                        <div class="group relative flex flex-col items-center justify-center p-3 rounded-xl bg-zinc-950/50 border border-zinc-800/50" title="{{ $badge->name }}">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center bg-zinc-900 mb-2">
                                <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}" class="w-full h-full object-cover">
                            </div>
                            <span class="text-[9px] font-black text-zinc-400 group-hover:text-white uppercase tracking-widest text-center truncate w-full">
                                {{ current(explode(' ', $badge->name)) }}
                            </span>
                        </div>
                    @empty
                        <div class="col-span-3 text-zinc-500 text-sm italic">User has no badges.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
