@extends('layouts.admin')

@section('title', 'Create Format')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Create Format</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Add a new anime format</p>
        </div>
        
        <a href="{{ route('admin.formats.index') }}" 
           class="bg-zinc-800 hover:bg-zinc-700 text-white px-4 py-2 rounded-xl transition-all active:scale-95 flex items-center gap-2 text-sm uppercase tracking-widest font-bold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to List
        </a>
    </div>

    <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden p-6">
        <form action="{{ route('admin.formats.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="name" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm @error('name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/50 @enderror">
                <p class="text-zinc-500 text-[10px] uppercase tracking-wider mt-1">E.g., TV, Movie, OVA, ONA</p>
                @error('name')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>



            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95 flex items-center gap-2 text-sm uppercase tracking-widest font-bold">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Save Format
                </button>
            </div>
        </form>
    </div>
</div>


@endsection
