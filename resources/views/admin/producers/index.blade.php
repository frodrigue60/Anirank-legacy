@extends('layouts.admin')

@section('title', 'Producers')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Producers & Committees</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Production Companies &
                    Stakeholders</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.producers.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-primary/20 hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined mr-2">add</span>
                    NEW PRODUCER
                </a>
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
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">Producer
                                    Details</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">
                                    Posts</th>
                                <th class="px-6 py-4 text-xs font-bold text-zinc-400 uppercase tracking-widest text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @foreach ($producers as $producer)
                                <tr class="hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-mono text-zinc-500">#{{ $producer->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-500 border border-zinc-700 overflow-hidden">
                                                @if ($producer->logo_url)
                                                    <img src="{{ $producer->logo_url }}" alt=""
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <span class="material-symbols-outlined text-sm">corporate_fare</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-white">
                                                    {{ $producer->name }}
                                                </span>
                                                <span class="text-[10px] text-zinc-500 font-medium">Production
                                                    Company</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-800 text-zinc-300 border border-zinc-700">
                                            {{ $producer->animes->count() }} animes
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if (Auth::user()->isStaff())
                                                <a href="{{ route('admin.producers.edit', $producer->id) }}"
                                                    class="p-2 bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-blue-500"
                                                    title="Edit Producer">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </a>
                                                @if (Auth::user()->isAdmin())
                                                    <form action="{{ route('admin.producers.destroy', $producer->id) }}"
                                                        method="post" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Delete producer?')"
                                                            class="p-2 bg-zinc-800 hover:bg-red-600 text-zinc-400 hover:text-white rounded-lg transition-all border border-zinc-700 hover:border-red-500"
                                                            title="Delete Producer">
                                                            <span class="material-symbols-outlined">delete</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="bg-zinc-950/50 px-6 py-8 border-t border-zinc-800">
                    <div class="flex justify-center">
                        {{ $producers->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
