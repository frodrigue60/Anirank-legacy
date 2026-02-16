@extends('layouts.admin')

@section('title', 'Add Studio')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add Studio</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Register a new animation
                production house</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.studios.store') }}" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <i class="fa-solid fa-palette mr-2 text-blue-500"></i> STUDIO DETAILS
                    </h3>

                    <div class="space-y-2">
                        <label for="nameStudio"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Studio Name</label>
                        <input type="text" name="name" id="nameStudio" required value="{{ old('name') }}"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 @error('name') border-red-500 @enderror"
                            placeholder="e.g. Kyoto Animation">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <i class="fa-solid fa-save"></i>
                        SAVE STUDIO ENTRY
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
