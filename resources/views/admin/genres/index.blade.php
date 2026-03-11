@extends('layouts.admin')

@section('title', 'Genres Management')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Genres</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Manage Anime Genres</p>
        </div>
        
        <a href="{{ route('admin.genres.create') }}" 
           class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95 flex items-center gap-2 text-sm uppercase tracking-widest font-bold">
            <span class="material-symbols-outlined text-sm">add</span>
            New Genre
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-2xl text-sm" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-950/50 border-b border-zinc-800">
                        <th class="p-4 text-xs font-black text-zinc-500 uppercase tracking-widest border-r border-zinc-800/50">ID</th>
                        <th class="p-4 text-xs font-black text-zinc-500 uppercase tracking-widest border-r border-zinc-800/50">Name</th>
                        <th class="p-4 text-xs font-black text-zinc-500 uppercase tracking-widest border-r border-zinc-800/50">Slug</th>
                        <th class="p-4 text-xs font-black text-zinc-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($genres as $genre)
                    <tr class="hover:bg-zinc-800/30 transition-colors group">
                        <td class="p-4 text-sm text-zinc-400 border-r border-zinc-800/50 font-mono">{{ $genre->id }}</td>
                        <td class="p-4 text-sm text-white font-bold border-r border-zinc-800/50">
                            {{ $genre->name }}
                        </td>
                        <td class="p-4 text-sm text-zinc-400 border-r border-zinc-800/50 font-mono">
                            {{ $genre->slug }}
                        </td>
                        <td class="p-4 relative">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.genres.edit', $genre) }}" 
                                   class="p-2 text-zinc-400 hover:text-white hover:bg-zinc-700 rounded-xl transition-all"
                                   title="Edit Genre">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this genre?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-zinc-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all"
                                            title="Delete Genre">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-zinc-500">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">category</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No genres found matching your criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($genres->hasPages())
        <div class="p-4 border-t border-zinc-800 bg-zinc-950/30">
            {{ $genres->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
