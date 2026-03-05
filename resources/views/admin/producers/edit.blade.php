@extends('layouts.admin')

@section('title', 'Edit Producer')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Edit Producer</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">{{ $producer->name }}</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.producers.update', $producer->id) }}" class="space-y-8"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">corporate_fare</span> PRODUCER DETAILS
                    </h3>

                    <div class="space-y-2">
                        <label for="nameProducer"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Producer Name</label>
                        <input type="text" name="name" id="nameProducer" required
                            value="{{ old('name', $producer->name) }}"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 @error('name') border-red-500 @enderror"
                            placeholder="e.g. Aniplex">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="logo"
                                    class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Logo
                                    File</label>
                                <input type="file" name="logo" id="logo"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm @error('logo') border-red-500 @enderror">
                                @error('logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="logo_src"
                                    class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Logo URL
                                    (External)</label>
                                <input type="url" name="logo_src" id="logo_src"
                                    value="{{ old('logo_src', filter_var($producer->logo, FILTER_VALIDATE_URL) ? $producer->logo : '') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 @error('logo_src') border-red-500 @enderror"
                                    placeholder="https://example.com/logo.png">
                                @error('logo_src')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest text-center">Current
                                Logo</label>
                            <div
                                class="flex flex-col items-center justify-center p-4 bg-zinc-950/30 border border-zinc-800 rounded-3xl min-h-[150px]">
                                @if ($producer->logo_url)
                                    <img src="{{ $producer->logo_url }}"
                                        class="max-w-[120px] max-h-[120px] rounded-xl object-contain shadow-lg"
                                        alt="Current Logo">
                                    <span
                                        class="text-[10px] text-zinc-500 mt-2 font-mono break-all">{{ $producer->logo }}</span>
                                @else
                                    <div
                                        class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-600 border border-zinc-700">
                                        <span class="material-symbols-outlined text-4xl">corporate_fare</span>
                                    </div>
                                    <span class="text-[10px] text-zinc-500 mt-2 uppercase font-black tracking-widest">No
                                        logo assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-4">
                    <button
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">check_circle</span>
                        UPDATE PRODUCER ENTRY
                    </button>
                    <a href="{{ route('admin.producers.index') }}"
                        class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest border border-zinc-700">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
