@extends('layouts.admin')

@section('title', isset($announcement) ? 'Edit Announcement' : 'Create Announcement')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">{{ isset($announcement) ? 'Edit Announcement' : 'Create New Announcement' }}</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Manage sidebar notifications & marketing</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ isset($announcement) ? route('admin.announcements.update', $announcement->id) : route('admin.announcements.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @if(isset($announcement))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">article</span> CORE INFORMATION
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label for="title" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Title</label>
                            <input type="text" name="title" id="title" required value="{{ old('title', $announcement->title ?? '') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. Welcome to Anirank!">
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="content" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Content (Markdown supported)</label>
                            <textarea name="content" id="content" rows="4"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                                placeholder="Describe the announcement...">{{ old('content', $announcement->content ?? '') }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label for="type" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Type / Visual Style</label>
                            <select name="type" id="type" required
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                                <option value="info" {{ old('type', $announcement->type ?? '') == 'info' ? 'selected' : '' }}>Info (Blue)</option>
                                <option value="success" {{ old('type', $announcement->type ?? '') == 'success' ? 'selected' : '' }}>Success (Green)</option>
                                <option value="warning" {{ old('type', $announcement->type ?? '') == 'warning' ? 'selected' : '' }}>Warning (Amber)</option>
                                <option value="danger" {{ old('type', $announcement->type ?? '') == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                                <option value="event" {{ old('type', $announcement->type ?? '') == 'event' ? 'selected' : '' }}>Event (Purple + Glow)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="icon" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Icon (Material Symbols)</label>
                            <input type="text" name="icon" id="icon" value="{{ old('icon', $announcement->icon ?? '') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. campaign, event, star">
                        </div>

                        <div class="space-y-2">
                            <label for="url" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Action URL</label>
                            <input type="url" name="url" id="url" value="{{ old('url', $announcement->url ?? '') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="https://example.com/target">
                        </div>

                        <div class="space-y-2">
                            <label for="priority" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Priority (Higher = First)</label>
                            <input type="number" name="priority" id="priority" required value="{{ old('priority', $announcement->priority ?? 0) }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                        </div>
                    </div>

                    {{-- Background Image Section --}}
                    <div class="space-y-6 pt-4 border-t border-zinc-800/50">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">image</span> BACKGROUND IMAGE
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="image_file" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Upload local File</label>
                                <input type="file" name="image_file" id="image_file" accept="image/*"
                                    class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 file:transition-all cursor-pointer">
                            </div>

                            <div class="space-y-2">
                                <label for="image_url_field" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Or external URL</label>
                                <input type="url" name="image_url" id="image_url_field" value="{{ old('image_url', (isset($announcement) && filter_var($announcement->image, FILTER_VALIDATE_URL)) ? $announcement->image : '') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                    placeholder="https://example.com/bg.jpg">
                            </div>
                        </div>
                        
                        @if(isset($announcement) && $announcement->image_url)
                            <div class="p-4 bg-zinc-950/50 rounded-2xl border border-zinc-800">
                                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-3">Current Background Preview</p>
                                <div class="w-full max-w-sm aspect-video rounded-xl overflow-hidden border border-zinc-800">
                                    <img src="{{ $announcement->image_url }}" alt="" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Scheduling Section --}}
                    <div class="space-y-6 pt-4 border-t border-zinc-800/50">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">schedule</span> AVAILABILITY & STATUS
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label for="starts_at" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Starts at (Optional)</label>
                                <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', isset($announcement) && $announcement->starts_at ? $announcement->starts_at->format('Y-m-d\TH:i') : '') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                            </div>

                            <div class="space-y-2">
                                <label for="ends_at" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Ends at (Optional)</label>
                                <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', isset($announcement) && $announcement->ends_at ? $announcement->ends_at->format('Y-m-d\TH:i') : '') }}"
                                    class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                            </div>

                            <div class="flex items-center gap-3 pt-6">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $announcement->is_active ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-bold text-zinc-400 uppercase tracking-widest">Active Status</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-8 border-t border-zinc-800/50 flex flex-col md:flex-row gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined">save</span>
                        {{ isset($announcement) ? 'UPDATE ANNOUNCEMENT' : 'CREATE ANNOUNCEMENT' }}
                    </button>
                    <a href="{{ route('admin.announcements.index') }}"
                        class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
