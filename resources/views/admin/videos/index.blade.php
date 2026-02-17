@extends('layouts.admin')

@section('title', 'Video Gallery')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Video Content</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Visual Assets & Mirrors</p>
            </div>
        </div>
        <p class="text-zinc-400 mt-1">Video configuration for <span
                class="text-blue-400 font-semibold">{{ $songVariant->song->name }} {{ $songVariant->song->slug }}
                {{ $songVariant->slug }}</span></p>

        {{-- Table Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">ID
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Type</th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Source/Content
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @php
                            $video = $songVariant->video;
                        @endphp
                        @if ($video)
                            <tr class="hover:bg-zinc-800/30 transition-colors group">
                                <td class="px-6 py-4 text-sm font-mono text-zinc-500 text-center">#{{ $video->id }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-zinc-800 text-zinc-300 border border-zinc-700">
                                        {{ $video->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-300 font-mono truncate max-w-md">
                                        {{ $video->video_src ?? $video->embed_code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.videos.edit', $video->id) }}"
                                            class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </a>
                                        <a href="{{ route('admin.videos.show', $video->id) }}"
                                            class="p-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </a>
                                        <form action="{{ route('admin.videos.destroy', $video->id) }}" method="post"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete video?')"
                                                class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-zinc-500 italic">No video configured for this variant.</p>
                                    <a href="{{ route('admin.variants.videos.add', $song_variant->id) }}"
                                        class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-400 font-bold">
                                        <span class="material-symbols-outlined mr-2">add</span> ADD VIDEO NOW
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
