@extends('layouts.admin')

@section('title', 'Edit Season')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Edit Season</h1>
            <label for="season-name" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Season
                Name</label>
            <input type="text" name="season_name" id="season-name" required value="{{ old('season_name', $season->name) }}"
                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                placeholder="e.g. SPRING, SUMMER, FALL, WINTER">
        </div>

        {{-- Action --}}
        <div class="pt-4 flex flex-col sm:flex-row gap-4">
            <button
                class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                <i class="fa-solid fa-check-double"></i>
                SAVE CHANGES
            </button>
            <a href="{{ route('admin.seasons.index') }}"
                class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest border border-zinc-700">
                CANCEL
            </a>
        </div>
        </form>
    </div>
    </div>
@endsection
