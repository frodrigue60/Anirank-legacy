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
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-8">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="userPass"
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest">Change
                                Password</label>
                            <input type="password" name="password" id="userPass"
                                class="block w-full bg-zinc-950/50 border border-zinc-800 text-white rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-sm h-12"
                                placeholder="Leave blank to keep current">
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

                        {{-- User Badges --}}
                        <div class="space-y-4 col-span-2 pt-6 border-t border-zinc-800/50">
                            <label
                                class="block text-sm font-bold text-zinc-400 uppercase tracking-widest flex items-center">
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
