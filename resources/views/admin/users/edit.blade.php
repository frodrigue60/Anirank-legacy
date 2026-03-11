@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Modify Account</h1>
            <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">{{ $user->name }}</p>
        </div>


        {{-- Form Card --}}
        <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 rounded-3xl shadow-xl overflow-hidden p-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                {{-- User Credentials --}}
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                        <span class="material-symbols-outlined mr-2 text-blue-500">lock_person</span> ACCOUNT DETAILS
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nameUser"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Full Name</label>
                            <input type="text" name="name" id="nameUser" required
                                value="{{ old('name', $user->name) }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="John Doe">
                        </div>

                        <div class="space-y-2">
                            <label for="emailUser"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Email
                                Address</label>
                            <input type="email" name="email" id="emailUser" required
                                value="{{ old('email', $user->email) }}"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="john@example.com">
                        </div>
                    </div>

                        <div class="space-y-2 col-span-2" x-data="{
                            password: '',
                            copied: false,
                            generatePassword() {
                                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
                                let pass = '';
                                for (let i = 0; i < 16; i++) {
                                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                                }
                                this.password = pass;
                            },
                            copyToClipboard() {
                                if (!this.password) return;
                                navigator.clipboard.writeText(this.password).then(() => {
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000);
                                });
                            }
                        }">
                            <label for="userPass" class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">
                                Change Password / Reset
                            </label>
                            
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="relative flex-1">
                                    <input type="text" name="password" id="userPass" x-model="password"
                                        class="block w-full bg-zinc-950/50 border border-zinc-800 text-white font-mono rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                        placeholder="Leave blank to keep current">
                                    
                                    <button type="button" @click="copyToClipboard" :class="copied ? 'text-emerald-500' : 'text-zinc-500 hover:text-white'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors p-1" title="Copy to clipboard">
                                        <span class="material-symbols-outlined text-sm" x-text="copied ? 'check' : 'content_copy'">content_copy</span>
                                    </button>
                                </div>
                                
                                <button type="button" @click="generatePassword"
                                    class="h-12 px-6 flex items-center justify-center gap-2 bg-blue-600/10 hover:bg-blue-600 text-blue-500 hover:text-white border border-blue-600/20 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest whitespace-nowrap">
                                    <span class="material-symbols-outlined text-sm">autorenew</span>
                                    Generate
                                </button>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-1">Leave the field blank if you do not wish to reset the password.</p>
                        </div>

                        <div class="space-y-4 col-span-2">
                            <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Platform
                                Roles</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                    $userRoleIds = $user->roles->pluck('id')->toArray();
                                @endphp
                                @foreach ($roles as $role)
                                    <label
                                        class="relative flex items-center p-4 rounded-2xl bg-zinc-950/50 border border-zinc-800 cursor-pointer group hover:border-blue-500/50 transition-all">
                                        <input type="checkbox" name="role_id[]" value="{{ $role->id }}"
                                            {{ in_array($role->id, old('role_id', $userRoleIds)) ? 'checked' : '' }}
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

                        {{-- Profile Appearance Section --}}
                        <div class="space-y-8 col-span-2 pt-8 border-t border-zinc-800/50">
                            <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-widest flex items-center">
                                <span class="material-symbols-outlined mr-2 text-blue-500">image</span> PROFILE APPEARANCE
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                {{-- Avatar --}}
                                <div class="space-y-4">
                                    <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">User
                                        Avatar</label>
                                    <div class="flex items-center gap-6">
                                        <div class="relative group">
                                            <div
                                                class="w-20 h-20 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center overflow-hidden">
                                                @if ($user->avatar_url)
                                                    <img src="{{ $user->avatar_url }}" alt="Avatar"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <span
                                                        class="material-symbols-outlined text-zinc-800 text-3xl">account_circle</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1 space-y-3">
                                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                                class="block w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 cursor-pointer">
                                            <input type="url" name="avatar_src" id="avatar_src"
                                                value="{{ old('avatar_src', filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : '') }}"
                                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-xs h-10"
                                                placeholder="External Avatar URL">
                                        </div>
                                    </div>
                                </div>

                                {{-- Banner --}}
                                <div class="space-y-4">
                                    <label class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Profile
                                        Banner</label>
                                    <div class="flex flex-col gap-4">
                                        <div
                                            class="w-full h-24 rounded-2xl bg-zinc-950 border border-zinc-800 overflow-hidden relative">
                                            @if ($user->banner_url)
                                                <img src="{{ $user->banner_url }}" alt="Banner"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-zinc-900/50">
                                                    <span
                                                        class="material-symbols-outlined text-zinc-800 text-3xl">image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-1 gap-3">
                                            <input type="file" name="banner" id="banner" accept="image/*"
                                                class="block w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 cursor-pointer">
                                            <input type="url" name="banner_src" id="banner_src"
                                                value="{{ old('banner_src', filter_var($user->banner, FILTER_VALIDATE_URL) ? $user->banner : '') }}"
                                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-xs h-10"
                                                placeholder="External Banner URL">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- User Badges --}}
                        <div class="space-y-4 col-span-2 pt-8 border-t border-zinc-800/50">
                            <label class="text-sm font-bold text-zinc-400 uppercase tracking-widest flex items-center">
                                <span class="material-symbols-outlined mr-2 text-yellow-500">workspace_premium</span> USER
                                BADGES
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @php
                                    $userBadgeIds = $user->badges->pluck('id')->toArray();
                                @endphp
                                @foreach ($badges as $badge)
                                    <label
                                        class="relative flex flex-col items-center p-4 rounded-2xl bg-zinc-950/50 border border-zinc-800 cursor-pointer group hover:border-yellow-500/50 transition-all text-center">
                                        <input type="checkbox" name="badge_id[]" value="{{ $badge->id }}"
                                            {{ in_array($badge->id, old('badge_id', $userBadgeIds)) ? 'checked' : '' }}
                                            class="absolute top-3 right-3 form-checkbox h-4 w-4 text-yellow-600 rounded-md border-zinc-700 bg-zinc-900 focus:ring-offset-0 focus:ring-yellow-500">

                                        <div
                                            class="w-12 h-12 rounded-xl bg-zinc-900 border border-zinc-800 mb-3 flex items-center justify-center overflow-hidden shadow-inner group-hover:scale-110 transition-transform">
                                            <img src="{{ $badge->icon_url }}" alt="{{ $badge->name }}"
                                                class="w-full h-full object-cover">
                                        </div>

                                        <span
                                            class="block text-[10px] font-black text-white group-hover:text-yellow-400 transition-colors uppercase tracking-widest leading-tight">
                                            {{ $badge->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-4">
                    <button
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-blue-900/20 active:scale-[0.98] flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                        <span class="material-symbols-outlined mr-2">check_circle</span>
                        UPDATE USER ACCOUNT
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-widest border border-zinc-700">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
