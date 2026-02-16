@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (Auth::check() && Auth::user()->isStaff())
            {{-- Management Hub --}}
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <div class="h-6 w-1 bg-blue-600 rounded-full"></div>
                    <h2 class="text-xl font-bold text-white uppercase tracking-wider text-sm">Content Management Hub</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Posts Card --}}
                    <a href="{{ route('admin.posts.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-blue-600/20 flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg shadow-blue-900/10">
                                <i class="fa-solid fa-clapperboard text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Posts</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Systematically manage the anime library, seasonal
                            entries, and core metadata.</p>
                    </a>

                    {{-- Artists Card --}}
                    <a href="{{ route('admin.artists.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-600/20 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-lg shadow-emerald-900/10">
                                <i class="fa-solid fa-microphone-lines text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Artists</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Configure performers, music groups, and their
                            associated theme portfolios.</p>
                    </a>

                    {{-- Global Parameters Card --}}
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.years.index') }}"
                            class="group p-4 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all text-center">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-800 mx-auto mb-3 flex items-center justify-center text-zinc-400 group-hover:bg-white group-hover:text-black transition-all">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest">Years</span>
                        </a>
                        <a href="{{ route('admin.seasons.index') }}"
                            class="group p-4 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all text-center">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-800 mx-auto mb-3 flex items-center justify-center text-zinc-400 group-hover:bg-white group-hover:text-black transition-all">
                                <i class="fa-solid fa-cloud-sun"></i>
                            </div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest">Seasons</span>
                        </a>
                    </div>

                    {{-- Users Management Card --}}
                    <a href="{{ route('admin.users.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-amber-600/20 flex items-center justify-center text-amber-500 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-lg shadow-amber-900/10">
                                <i class="fa-solid fa-user-shield text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-amber-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">User Directory</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Audit user activity, adjust permissions, and manage
                            platform staff accounts.</p>
                    </a>

                    {{-- Reports Card --}}
                    <a href="{{ route('admin.reports.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-rose-600/20 flex items-center justify-center text-rose-500 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-lg shadow-rose-900/10">
                                <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-rose-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Reports</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Moderate content by reviewing community flags and
                            system-generated reports.</p>
                    </a>

                    {{-- Requests Card --}}
                    <a href="{{ route('admin.requests.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-600/20 flex items-center justify-center text-indigo-500 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-lg shadow-indigo-900/10">
                                <i class="fa-solid fa-envelope-open-text text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-indigo-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Requests</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Process content addition requests and community
                            suggestions.</p>
                    </a>

                    {{-- Producers Card --}}
                    <a href="{{ route('admin.producers.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-cyan-600/20 flex items-center justify-center text-cyan-500 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-lg shadow-cyan-900/10">
                                <i class="fa-solid fa-building text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-cyan-500 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Producers</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Manage anime production companies and their linked
                            entries.</p>
                    </a>

                    {{-- Studios Card --}}
                    <a href="{{ route('admin.studios.index') }}"
                        class="group p-6 bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-blue-400/20 flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg shadow-blue-900/10">
                                <i class="fa-solid fa-palette text-2xl"></i>
                            </div>
                            <i
                                class="fa-solid fa-arrow-right text-zinc-700 group-hover:text-blue-400 transition-colors"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Studios</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">Organize animation studios and monitor their
                            seasonal
                            project portfolios.</p>
                    </a>
                </div>
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
