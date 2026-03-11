@extends('layouts.admin')

@section('title', 'Comment Report Details')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Report #{{ $report->id }}</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Audit & Resolution Details</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-6 flex items-center">
                        <span class="material-symbols-outlined mr-2">description</span> REPORT CONTENT
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <span class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter block mb-1">Subject
                                / Title</span>
                            <p class="text-lg font-bold text-white tracking-tight">{{ $report->title }}</p>
                        </div>

                        <div>
                            <span
                                class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter block mb-1">Description</span>
                            <div
                                class="p-4 bg-zinc-950/30 border border-zinc-800 rounded-2xl text-zinc-300 text-sm leading-relaxed whitespace-pre-line">
                                {{ $report->content }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if ($report->comment)
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-6 flex items-center">
                        <span class="material-symbols-outlined mr-2">forum</span> COMMENT CONTEXT
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <span
                                class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter block mb-1">Comment content</span>
                            <div
                                class="p-4 bg-zinc-950/30 border border-zinc-800 rounded-2xl text-zinc-300 text-sm leading-relaxed whitespace-pre-line">
                                {{ $report->comment->content }}
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-500 border border-zinc-700">
                                @if($report->comment->user->avatar)
                                    <img src="{{ $report->comment->user->avatar }}" class="w-full h-full object-cover rounded-full" alt="">
                                @else
                                    <span class="material-symbols-outlined text-sm">person</span>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-white">{{ $report->comment->user->name }}</span>
                                <span class="text-[10px] text-zinc-500">Comment Author</span>
                            </div>
                            
                            <div class="ml-auto">
                                <form action="{{ route('admin.comments.destroy', $report->comment->id) }}" method="post" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this comment permanentely?')"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-600/20 font-bold rounded-xl transition-all text-xs">
                                        <span class="material-symbols-outlined mr-1 text-sm">delete</span> DELETE COMMENT
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-6">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 shadow-xl">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-4 flex items-center">
                        <span class="material-symbols-outlined mr-2">bolt</span> MODERATION
                    </h3>

                    <div class="space-y-3">
                        @if ($report->status == 'pending')
                            <form action="{{ route('admin.comment-reports.toggle', $report->id) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/20">
                                    <span class="material-symbols-outlined mr-2">check_circle</span> MARK AS RESOLVED
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.comment-reports.toggle', $report->id) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-xl transition-all border border-zinc-700">
                                    <span class="material-symbols-outlined mr-2 text-zinc-500">undo</span> REOPEN REPORT
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.comment-reports.destroy', $report->id) }}" method="post" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Dismiss and delete permanentely?')"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-rose-600/10 hover:bg-rose-600 text-rose-500 hover:text-white border border-rose-600/20 font-bold rounded-xl transition-all">
                                <span class="material-symbols-outlined mr-2">delete</span> DISMISS REPORT
                            </button>
                        </form>
                    </div>

                    <div class="mt-6 pt-6 border-t border-zinc-800">
                        <span class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter block mb-2">Attached
                            Source</span>
                        @if ($report->comment && $report->comment->song)
                        <a href="{{ $report->source }}" target="_blank"
                            class="group block p-3 bg-zinc-950/30 rounded-2xl border border-zinc-800 hover:border-blue-500/50 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col overflow-hidden">
                                    <span
                                        class="text-xs font-bold text-white truncate group-hover:text-blue-400 transition-colors">Song: {{ $report->comment->song->name }}</span>
                                    <span class="text-[10px] text-zinc-500 truncate">Go to source page</span>
                                </div>
                                <span class="material-symbols-outlined ml-auto text-zinc-600 group-hover:text-blue-400">open_in_new</span>
                            </div>
                        </a>
                        @else
                        <span class="text-sm text-zinc-500 italic">Source missing or deleted</span>
                        @endif
                    </div>
                </div>

                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 shadow-xl">
                    <span class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter block mb-2">Reporter
                        Information</span>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-500 border border-zinc-700">
                            @if($report->user && $report->user->avatar)
                                <img src="{{ $report->user->avatar }}" class="w-full h-full object-cover rounded-full" alt="">
                            @else
                                <span class="material-symbols-outlined text-sm">person</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white">{{ $report->user ? $report->user->name : '#' . $report->user_id }}</span>
                            <span class="text-[10px] text-zinc-500">System Verified ID</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
