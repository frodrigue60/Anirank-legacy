@extends('layouts.admin')

@section('title', 'Add Season')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Custom Header Section --}}
        <div class="space-y-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add Season</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Register a new broadcast period
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.seasons.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="season-name" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Season
                        Name</label>
                    <input type="text" name="season_name" id="season-name" required value="{{ old('season_name') }}"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                        placeholder="e.g. SPRING, SUMMER, FALL, WINTER">
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">save</span>
                        CREATE SEASON
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
