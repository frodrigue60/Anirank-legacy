@extends('layouts.admin')

@section('title', 'Manage Variants')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">
                    @if ($currentSong)
                        Variants: {{ $currentSong->name }}
                    @else
                        Song Variants
                    @endif
                </h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">
                    @if ($currentSong)
                        Version Control for {{ $currentSong->slug }}
                    @else
                        Audio Version Control & Registry
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($currentSong)
                    <form action="{{ route('admin.variants.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="song_id" value="{{ $currentSong->id }}">
                        <button type="submit"
                            class="px-6 py-3 bg-primary hover:bg-primary-hover text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-primary/20 active:scale-95 uppercase tracking-widest">
                            <span class="material-symbols-outlined mr-2 text-base">add</span>
                            Add New Variant
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            {{-- Table Card --}}
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/50 border-b border-zinc-800">
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">ID</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Variant Slug
                                </th>
                                @if (!$currentSong)
                                    <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Song
                                    </th>
                                @endif
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">
                                    Video Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse ($songVariants as $variant)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-mono text-zinc-500">#{{ $variant->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-white group-hover:text-primary transition-colors">{{ $variant->slug }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-0.5">Version
                                                #{{ $variant->version_number }}</span>
                                        </div>
                                    </td>
                                    @if (!$currentSong)
                                        <td class="px-6 py-4">
                                            @if ($variant->song)
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-zinc-300">{{ $variant->song->name }}</span>
                                                    @if ($variant->song->post)
                                                        <span
                                                            class="text-[10px] text-zinc-500 uppercase font-bold">{{ $variant->song->post->title }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-zinc-600 italic">Unlinked</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-center">
                                        @if ($variant->video)
                                            <a href="{{ route('admin.variants.videos', $variant->id) }}"
                                                class="inline-flex items-center px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all tracking-widest">
                                                <span class="material-symbols-outlined text-sm mr-1.5">play_circle</span>
                                                CONFIGURED
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-zinc-800 text-zinc-500 text-[10px] font-black uppercase rounded-lg border border-zinc-700 tracking-widest">
                                                <span class="material-symbols-outlined text-sm mr-1.5">error</span>
                                                NO VIDEO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2 shrink-0">
                                            <a href="{{ route('admin.variants.edit', $variant->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </a>
                                            <a href="{{ route('admin.variants.videos.add', $variant->id) }}"
                                                class="p-2 bg-zinc-800 hover:bg-emerald-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-emerald-500"
                                                title="Add/Update Video">
                                                <span class="material-symbols-outlined text-sm">movie</span>
                                            </a>
                                            <form action="{{ route('admin.variants.destroy', $variant->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Delete this variant? All associated videos will be unlinked.')"
                                                    class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $currentSong ? 4 : 5 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="material-symbols-outlined text-5xl text-zinc-700 mb-4">layers_clear</span>
                                            <h3 class="text-white font-bold uppercase tracking-widest text-sm">No Variants
                                                Found</h3>
                                            <p class="text-zinc-500 text-xs mt-2">Add a new version for this song to begin
                                                configuration.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($songVariants->hasPages())
                    <div class="px-6 py-4 bg-zinc-950/50 border-t border-zinc-800 pagination-custom">
                        {{ $songVariants->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
    </style>
@endpush
