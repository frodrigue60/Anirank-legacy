@extends('layouts.admin')

@section('title', 'Edit Tournament')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Edit Tournament</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Update bracket settings</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="post" action="{{ route('admin.tournaments.update', $tournament->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Tournament
                        Name</label>
                    <input type="text" name="name" id="name" required
                        value="{{ old('name', $tournament->name) }}"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">URL
                        Slug</label>
                    <input type="text" name="slug" id="slug" required
                        value="{{ old('slug', $tournament->slug) }}"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12">
                </div>

                <div class="space-y-2">
                    <label for="description"
                        class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm">{{ old('description', $tournament->description) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label for="type_filter" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Song
                        Type Filter</label>
                    <select name="type_filter" id="type_filter"
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 appearance-none">
                        <option value="" {{ old('type_filter', $tournament->type_filter) == '' ? 'selected' : '' }}>
                            Any Type (No Filter)</option>
                        <option value="OP" {{ old('type_filter', $tournament->type_filter) == 'OP' ? 'selected' : '' }}>
                            Openings Only (OP)</option>
                        <option value="ED" {{ old('type_filter', $tournament->type_filter) == 'ED' ? 'selected' : '' }}>
                            Endings Only (ED)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="status"
                        class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Status</label>
                    <select name="status" id="status" required
                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12 appearance-none">
                        <option value="draft" {{ old('status', $tournament->status) == 'draft' ? 'selected' : '' }}>Draft
                        </option>
                        <option value="active" {{ old('status', $tournament->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="completed" {{ old('status', $tournament->status) == 'completed' ? 'selected' : '' }}>
                            Completed</option>
                    </select>
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-indigo-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">save</span>
                        UPDATE TOURNAMENT
                    </button>

                    <a href="{{ route('admin.tournaments.index') }}"
                        class="mt-4 block text-center text-sm font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-widest">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
