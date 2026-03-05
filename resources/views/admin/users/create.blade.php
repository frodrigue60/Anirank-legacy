@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Create User Account</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Provision a new user with specific
                roles</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- User Credentials --}}
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">lock_person</span> ACCOUNT DETAILS
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nameUser"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Full Name</label>
                            <input type="text" name="name" id="nameUser" required value="{{ old('name') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="John Doe">
                        </div>

                        <div class="space-y-2">
                            <label for="emailUser"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Email
                                Address</label>
                            <input type="email" name="email" id="emailUser" required value="{{ old('email') }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="john@example.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="userPass"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Secure
                                Password</label>
                            <input type="password" name="password" id="userPass" required
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="••••••••">
                        </div>

                        <div class="space-y-4 col-span-2">
                            <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Platform
                                Roles</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($roles as $role)
                                    <label
                                        class="relative flex items-center p-4 rounded-2xl bg-zinc-950/50 border border-zinc-800 cursor-pointer group hover:border-blue-500/50 transition-all">
                                        <input type="checkbox" name="role_id[]" value="{{ $role->id }}"
                                            {{ old('role_id') && in_array($role->id, old('role_id')) ? 'checked' : '' }}
                                            class="form-checkbox h-5 w-5 text-blue-600 rounded-lg border-zinc-700 bg-zinc-900 focus:ring-offset-0 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <span
                                                class="block text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ $role->name }}</span>
                                            <span
                                                class="block text-[10px] text-zinc-500 uppercase tracking-tighter">{{ $role->slug }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Profile Images Section --}}
                    <div class="space-y-6 pt-4 border-t border-zinc-800/50">
                        <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                            <span class="material-symbols-outlined mr-2 text-blue-500">image</span> PROFILE APPEARANCE
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            {{-- Avatar --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">User
                                    Avatar</label>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="file" name="avatar" id="avatar" accept="image/*"
                                        class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 file:transition-all cursor-pointer">
                                    <input type="url" name="avatar_src" id="avatar_src" value="{{ old('avatar_src') }}"
                                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-xs"
                                        placeholder="External Avatar URL">
                                </div>
                            </div>

                            {{-- Banner --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Profile
                                    Banner</label>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="file" name="banner" id="banner" accept="image/*"
                                        class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 file:transition-all cursor-pointer">
                                    <input type="url" name="banner_src" id="banner_src" value="{{ old('banner_src') }}"
                                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-xs"
                                        placeholder="External Banner URL">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">person_add</span>
                        CREATE USER ACCOUNT
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
