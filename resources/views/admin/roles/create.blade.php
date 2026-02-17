@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Create New Role</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Define access levels & system
                permissions</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-8">
                @csrf

                {{-- Role Details --}}
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">security</span> ROLE IDENTIFICATION
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nameRole"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Role Name</label>
                            <input type="text" name="name" id="nameRole" required value="{{ old('name') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. Moderator">
                        </div>

                        <div class="space-y-2">
                            <label for="slugRole"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Slug
                                (Optional)</label>
                            <input type="text" name="slug" id="slugRole" value="{{ old('slug') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="e.g. moderator">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="descriptionRole"
                            class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Description</label>
                        <textarea name="description" id="descriptionRole" rows="4"
                            class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm"
                            placeholder="Describe the responsibilities of this role...">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-4">
                    <button
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">add_moderator</span>
                        CREATE SYSTEM ROLE
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                        class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest border border-zinc-700">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
