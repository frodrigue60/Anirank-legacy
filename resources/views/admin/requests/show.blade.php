@extends('layouts.admin')

@section('title', 'Request Details')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white uppercase tracking-widest">{{ $userRequest->title }}</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Request #{{ $userRequest->id }} •
                User Content Proposal</p>
        </div>

        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden shadow-xl">
            {{-- Content Section --}}
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">mail_lock</span> REQUEST CONTENT
                    </h3>
                    <div class="flex items-center gap-2">
                        @if ($userRequest->status == 'pending')
                            <span
                                class="flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 border border-amber-500/20 rounded-full text-[10px] font-black uppercase tracking-widest text-amber-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending Review
                            </span>
                        @else
                            <span
                                class="flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-widest text-emerald-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Attended
                            </span>
                        @endif
                    </div>
                </div>

                <div
                    class="relative p-8 bg-zinc-950/30 border border-zinc-800 rounded-3xl overflow-hidden group hover:border-zinc-700/50 transition-colors">
                    {{-- Side Accent --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500/40"></div>

                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <h4 class="text-xl font-bold text-white tracking-tight leading-none uppercase tracking-widest">
                                {{ $userRequest->title }}
                            </h4>
                            <span
                                class="material-symbols-outlined text-zinc-800 text-4xl group-hover:text-blue-500/10 transition-colors select-none">format_quote</span>
                        </div>
                        <p class="text-zinc-300 text-base leading-relaxed whitespace-pre-wrap font-medium">
                            {{ $userRequest->content }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Info & Action Footer --}}
            <div
                class="bg-zinc-950/50 px-8 py-6 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-400 border border-zinc-700">
                        <span class="material-symbols-outlined text-xs">person</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-white">{{ $userRequest->user->name }}</span>
                        <span class="text-[10px] text-zinc-500 uppercase font-black tracking-tighter">Member since
                            {{ $userRequest->user->created_at->format('M Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    @if ($userRequest->status == 'pending')
                        <form action="{{ route('admin.requests.attend', $userRequest->id) }}" method="post"
                            class="flex-1 sm:flex-none">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                                <span class="material-symbols-outlined mr-2">check_circle</span> MARK AS READ
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.requests.destroy', $userRequest->id) }}" method="post"
                        class="flex-1 sm:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Permanentely delete this request?')"
                            class="w-full inline-flex items-center justify-center px-6 py-2 bg-zinc-800 hover:bg-rose-600 text-zinc-400 hover:text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all border border-zinc-700 hover:border-rose-500 active:scale-95">
                            <span class="material-symbols-outlined mr-2">delete</span> DELETE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
