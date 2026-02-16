@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Admin Dashboard</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Platform Overview & Statistics
                </p>
            </div>
            {{-- <div
                class="flex items-center gap-3 px-4 py-2 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-2xl">
                <span class="w-2 h-2 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                <span class="text-xs font-bold text-zinc-300 uppercase tracking-widest">
                    SESSION ACTIVE: <span class="text-white font-black">{{ Auth::user()->name }}</span>
                </span>
            </div> --}}
        </div>

        @if (Auth::check() && Auth::user()->isStaff())
            {{-- Quick Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                {{-- Posts Stat --}}
                <x-admin.stat-card icon="movie" color="blue" label="Total Posts" value="{{ \App\Models\Post::count() }}" />
                <x-admin.stat-card icon="music_note" color="purple" label="Total Songs"
                    value="{{ \App\Models\Song::count() }}" />
                <x-admin.stat-card icon="mic" color="emerald" label="Total Artists"
                    value="{{ \App\Models\Artist::count() }}" />
                <x-admin.stat-card icon="group" color="amber" label="Total Users"
                    value="{{ \App\Models\User::count() }}" />
                <x-admin.stat-card icon="factory" color="cyan" label="Producers"
                    value="{{ \App\Models\Producer::count() }}" />
                <x-admin.stat-card icon="business" color="indigo" label="Studios"
                    value="{{ \App\Models\Studio::count() }}" />
            </div>
        @else
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-3xl p-12 text-center max-w-2xl mx-auto">
                <div
                    class="w-20 h-20 bg-rose-500/20 rounded-3xl flex items-center justify-center text-rose-500 mx-auto mb-6">
                    <i class="fa-solid fa-lock text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-rose-400 mb-3">Access Restricted</h3>
                <p class="text-white/60 mb-8">You do not have the required staff privileges to access this area.</p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center px-6 py-3 bg-white text-black font-bold rounded-2xl hover:bg-zinc-200 transition-all active:scale-95">
                    RETURN TO HOME
                </a>
            </div>
        @endif
    </div>
@endsection
