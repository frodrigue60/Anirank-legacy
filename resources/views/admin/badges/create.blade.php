@extends('layouts.admin')

@section('title', 'Create Badge')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Create New Badge</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Define rewards & achievements for
                users</p>
        </div>

        {{-- Form Card --}}
        <div
            class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8 transition-all hover:border-zinc-700">
            <form method="POST" action="{{ route('admin.badges.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Badge Details --}}
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">workspace_premium</span> BADGE
                        INFORMATION
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Badge Name</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. VIP Member">
                        </div>

                        <div class="space-y-2">
                            <label for="is_active"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Initial
                                Status</label>
                            <select name="is_active" id="is_active"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="description"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                            placeholder="Describe how users can earn this badge...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Icon Upload & URL --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Badge Icon</label>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="icon_src"
                                    class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Icon URL
                                    (Alternative to upload)</label>
                                <input type="url" name="icon_src" id="icon_src" value="{{ old('icon_src') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                    placeholder="https://example.com/badge.png">
                            </div>

                            <div class="flex items-center gap-6">
                                <div
                                    class="w-24 h-24 rounded-2xl bg-zinc-950 border-2 border-dashed border-zinc-800 flex items-center justify-center overflow-hidden group hover:border-blue-500 transition-colors">
                                    <span
                                        class="material-symbols-outlined text-zinc-600 group-hover:text-blue-500 transition-colors text-3xl">image</span>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="icon" id="icon" accept="image/*"
                                        class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 file:transition-all cursor-pointer">
                                    <p class="mt-2 text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">
                                        Recommended:
                                        Square image (256x256), PNG or WebP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined">save</span>
                        CREATE BADGE
                    </button>
                    <a href="{{ route('admin.badges.index') }}"
                        class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest border border-zinc-700">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
