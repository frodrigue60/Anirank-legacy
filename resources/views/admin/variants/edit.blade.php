@extends('layouts.admin')

@section('title', 'Edit Variant')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Edit Variant</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">{{ $songVariant->song->title }}
                ({{ $songVariant->slug }})</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.variants.update', [$songVariant->id]) }}"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-4 bg-zinc-950/30 p-6 rounded-2xl border border-zinc-800/50">
                    <div class="space-y-2">
                        <label for="version" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Theme
                            Version Number</label>
                        <input type="number" name="version_number" id="version"
                            value="{{ $songVariant->version_number }}"
                            class="block w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                            placeholder="e.g. 1, 2, 3...">
                        <p class="text-[10px] text-zinc-500 italic mt-1 font-medium italic">Usually represents V1, V2, etc.
                            for the same theme.</p>
                    </div>
                </div>

                {{-- Trigger --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">check_circle</span>
                        UPDATE VARIANT ENTRY
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
