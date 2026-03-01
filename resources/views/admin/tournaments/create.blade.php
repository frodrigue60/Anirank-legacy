@extends('layouts.admin')

@section('title', 'Create Tournament')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Create Tournament</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Setup a new voting bracket</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.tournaments.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Tournament
                        Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                        placeholder="e.g. Best Opening Spring 2026">
                </div>



                <div class="space-y-2">
                    <label for="description"
                        class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                        placeholder="Optional description...">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label for="size" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Bracket
                        Size</label>
                    <select name="size" id="size" required
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 appearance-none">
                        <option value="2" {{ old('size') == 2 ? 'selected' : '' }}>2 Songs (Final Only)</option>
                        <option value="4" {{ old('size') == 4 ? 'selected' : '' }}>4 Songs</option>
                        <option value="8" {{ old('size') == 8 ? 'selected' : '' }}>8 Songs</option>
                        <option value="16" {{ old('size') == 16 ? 'selected' : '' }}>16 Songs</option>
                        <option value="32" {{ old('size') == 32 ? 'selected' : '' }}>32 Songs</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="type_filter" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Song
                        Type Filter</label>
                    <select name="type_filter" id="type_filter"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 appearance-none">
                        <option value="" {{ old('type_filter') == '' ? 'selected' : '' }}>Any Type (No Filter)
                        </option>
                        <option value="OP" {{ old('type_filter') == 'OP' ? 'selected' : '' }}>Openings Only (OP)
                        </option>
                        <option value="ED" {{ old('type_filter') == 'ED' ? 'selected' : '' }}>Endings Only (ED)
                        </option>
                    </select>
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">save</span>
                        CREATE TOURNAMENT
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
