@extends('layouts.admin')

@section('title', 'Add Artist')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Add New Performer</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Register a new artist or music
                group</p>
        </div>


        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.artists.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">face</span> ARTIST IDENTITYAILS
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nameArtist"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Artist Name
                                (Romaji)</label>
                            <input type="text" name="name" id="nameArtist" required value="{{ old('name') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. LiSA">
                        </div>

                        <div class="space-y-2">
                            <label for="nameArtistsJp"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Artist Name
                                (JP)</label>
                            <input type="text" name="name_jp" id="nameArtistsJp" value="{{ old('name_jp') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. 織部 里沙">
                        </div>
                    </div>

                    {{-- Avatar Section --}}
                    <div class="space-y-6 pt-4">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">image</span> ARTIST AVATAR
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="avatar"
                                    class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Upload
                                    File</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 file:transition-all cursor-pointer">
                            </div>

                            <div class="space-y-2">
                                <label for="avatar_src"
                                    class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">External
                                    URL</label>
                                <input type="url" name="avatar_src" id="avatar_src" value="{{ old('avatar_src') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">save</span>
                        CREATE ARTIST ENTRY ENTRY
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
