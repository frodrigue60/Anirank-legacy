@extends('layouts.admin')

@section('title', 'Add Year')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add Year</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Define a new broadcast year for
                the timeline</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.years.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="year-name" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Year
                        (YYYY)</label>
                    <input type="number" name="year" id="year-name" min="1945" max="2030" required
                        value="{{ old('year') }}"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                        placeholder="e.g. 2024">
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <i class="fa-solid fa-save"></i>
                        SAVE YEAR
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
