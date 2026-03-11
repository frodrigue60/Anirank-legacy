@extends('layouts.admin')

@section('title', 'Comment Reports')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Comment Reports</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">User Reports & Moderation
                </p>
            </div>
        </div>
        <p class="text-zinc-400 mt-1">Review flagged comments for inappropriate content or spam.</p>

        {{-- Table Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">ID
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Context / Source
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Reporter ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @foreach ($reports as $report)
                            <tr class="hover:bg-zinc-800/30 transition-colors group">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500 text-center">#{{ $report->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        @if ($report->comment)
                                            <span class="text-sm font-bold text-white line-clamp-1">
                                                {{ Str::limit($report->comment->content, 50) }}
                                            </span>
                                            <span class="text-[10px] text-zinc-500 mt-1 font-medium truncate max-w-xs">
                                                By: {{ $report->comment->user->name ?? 'Unknown User' }}
                                            </span>
                                        @else
                                            <span class="text-sm text-zinc-500 italic line-clamp-1">Deleted Comment</span>
                                            <span class="text-[10px] text-zinc-600 mt-1 font-medium truncate max-w-xs">
                                                {{ $report->source }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-zinc-400">
                                    {{ $report->user_id }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.comment-reports.toggle', $report->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if ($report->status == 'pending')
                                            <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500 border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all">
                                                <span class="material-symbols-outlined text-sm mr-1.5">schedule</span>
                                                Pending
                                            </button>
                                        @else
                                            <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all">
                                                <span class="material-symbols-outlined text-sm mr-1.5">check_circle</span>
                                                Resolved
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (Auth::user()->isAdmin() || Auth::user()->isEditor())
                                            <a href="{{ route('admin.comment-reports.show', $report->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700"
                                                title="View Report Details">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                            <form action="{{ route('admin.comment-reports.destroy', $report->id) }}" method="post"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Dismiss and delete this report?')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500"
                                                    title="Delete Report">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="bg-zinc-950/50 px-6 py-6 border-t border-zinc-800">
                <div class="flex flex-col items-center">
                    <div class="text-zinc-500 text-xs mb-4">
                        Showing <span class="text-white font-bold">{{ $reports->firstItem() }}</span> to <span
                            class="text-white font-bold">{{ $reports->lastItem() }}</span> of <span
                            class="text-white font-bold">{{ $reports->total() }}</span> results
                    </div>
                    <div class="pagination-custom">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
