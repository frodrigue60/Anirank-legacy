@extends('layouts.admin')

@section('title', 'Add Variant')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add Version</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">{{ $song->title }} Edition</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="POST" action="{{ route('admin.variants.store', $song->id) }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="variant-type"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Variant Type</label>
                        <select name="type" id="variant-type" required
                            class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none appearance-none">
                            <option value="TV">TV Size</option>
                            <option value="FULL">Full Version</option>
                            <option value="INSTRUMENTAL">Instrumental</option>
                            <option value="COVER">Cover</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="variant-slug"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Slug (v1, v2,
                            etc.)</label>
                        <input type="text" name="slug" id="variant-slug" value="{{ old('slug', 'v1') }}" required
                            class="w-full bg-zinc-950 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t border-zinc-800 flex justify-end gap-3">
                    <a href="{{ route('admin.variants.manage', $song->id) }}"
                        class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-xl transition-all">CANCEL</a>
                    <button type="submit"
                        class="px-8 py-3 bg-primary hover:bg-primary-hover text-white font-black rounded-xl shadow-lg shadow-primary/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">CREATE
                        VARIANT</button>
                </div>
            </form>
        </div>
    </div>
@endsection
