<div class="flex flex-col md:flex-row gap-8 items-start">
    {{-- Sidebar Navigation --}}
    <div class="w-full md:w-64 shrink-0 bg-surface-darker/50 backdrop-blur-md border border-white/5 rounded-2xl p-4 sticky top-24">
        <nav class="flex flex-col gap-1">
            <h3 class="text-[10px] uppercase font-black text-white/30 px-3 mb-2 tracking-widest">Settings</h3>
            
            <button wire:click="setTab('profile')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'profile' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'profile' ? 'filled' : '' }}">account_circle</span>
                <span class="text-sm font-bold">Profile</span>
            </button>

            <button wire:click="setTab('account')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'account' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'account' ? 'filled' : '' }}">person</span>
                <span class="text-sm font-bold">Account</span>
            </button>

            <button wire:click="setTab('anime-manga')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'anime-manga' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'anime-manga' ? 'filled' : '' }}">movie</span>
                <span class="text-sm font-bold">Anime & Manga</span>
            </button>

            <button wire:click="setTab('lists')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'lists' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'lists' ? 'filled' : '' }}">list</span>
                <span class="text-sm font-bold">Lists</span>
            </button>

            <button wire:click="setTab('notifications')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'notifications' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'notifications' ? 'filled' : '' }}">notifications</span>
                <span class="text-sm font-bold">Notifications</span>
            </button>

            <button wire:click="setTab('import')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'import' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'import' ? 'filled' : '' }}">cloud_upload</span>
                <span class="text-sm font-bold">Import Lists</span>
            </button>

            <button wire:click="setTab('site-settings')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'site-settings' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'site-settings' ? 'filled' : '' }}">settings</span>
                <span class="text-sm font-bold">Site Settings</span>
            </button>

            <div class="h-px bg-white/5 my-2"></div>
            
            <h3 class="text-[10px] uppercase font-black text-white/30 px-3 mt-2 mb-2 tracking-widest">Apps</h3>
            
            <button wire:click="setTab('apps')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'apps' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'apps' ? 'filled' : '' }}">grid_view</span>
                <span class="text-sm font-bold">Apps</span>
            </button>

            <button wire:click="setTab('developer')" 
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ $activeTab === 'developer' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                <span class="material-symbols-outlined text-xl {{ $activeTab === 'developer' ? 'filled' : '' }}">code</span>
                <span class="text-sm font-bold">Developer</span>
            </button>
        </nav>
    </div>

    {{-- Content Area --}}
    <div class="flex-1 w-full flex flex-col gap-6">
        
        @if($activeTab === 'profile')
            {{-- Profile Settings --}}
            <div class="flex flex-col gap-6 animate-fade-in">
                <h2 class="text-2xl font-black text-white px-2">Profile</h2>
                
                {{-- PROFILE PICTURE CARD --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">account_circle</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Avatar</h3>
                    </div>

                    <form wire:submit="saveAvatar" class="space-y-6">
                        <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center">
                            {{-- Preview Area --}}
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white/10 shadow-2xl relative">
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-primary/20 flex items-center justify-center backdrop-blur-[2px]">
                                            <span class="text-[10px] font-black uppercase text-white tracking-widest">Preview</span>
                                        </div>
                                    @else
                                        <x-ui.image src="{{ $user->avatar_url }}" class="w-full h-full object-cover" />
                                    @endif
                                </div>
                                <div wire:loading wire:target="image" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center backdrop-blur-sm">
                                    <div class="w-8 h-8 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                                </div>
                            </div>

                            {{-- Upload Controls --}}
                            <div class="flex-1 space-y-4">
                                <div>
                                    <p class="text-sm text-white/60 mb-1">Allowed Formats: <span class="text-white">JPEG, PNG, WEBP</span></p>
                                    <p class="text-xs text-white/40 italic">Max size: 512KB. Optimal dimensions: 230x230</p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <label class="cursor-pointer bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-3 rounded-xl transition-all flex items-center gap-2 group">
                                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">cloud_upload</span>
                                        <span class="text-sm font-bold text-white">Choose Image</span>
                                        <input type="file" wire:model="image" class="hidden" accept="image/*">
                                    </label>

                                    @if($image)
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="bg-primary hover:bg-primary-light text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                                            <span wire:loading.remove wire:target="saveAvatar">Save Changes</span>
                                            <span wire:loading wire:target="saveAvatar" class="flex items-center gap-2">
                                                <div class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                                Saving...
                                            </span>
                                        </button>
                                    @endif
                                </div>

                                @error('image')
                                    <span class="text-xs text-red-500 mt-2 block animate-bounce">{{ $message }}</span>
                                @enderror

                                @if (session()->has('avatar_success'))
                                    <div class="p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-xs text-green-400 font-bold flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                        {{ session('avatar_success') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                {{-- BANNER PICTURE CARD --}}
                <div class="bg-surface-dark/30 p-6 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-primary">image</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Banner</h3>
                    </div>

                    <form wire:submit="saveBanner" class="space-y-6">
                        <div class="space-y-6">
                            {{-- Preview Area --}}
                            <div class="relative aspect-21/9 h-32 rounded-xl overflow-hidden border border-white/10 shadow-xl bg-surface-darker">
                                @if ($banner)
                                    <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-cover" />
                                    <div class="absolute inset-0 bg-primary/20 flex items-center justify-center backdrop-blur-[2px]">
                                        <span class="text-[10px] font-black uppercase text-white tracking-widest">New Banner Preview</span>
                                    </div>
                                @else
                                    <x-ui.image src="{{ $user->banner_url }}" class="w-full h-full object-cover" />
                                @endif

                                <div wire:loading wire:target="banner" class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-sm">
                                    <div class="w-8 h-8 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                                </div>
                            </div>

                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                <div>
                                    <p class="text-sm text-white/60 mb-1">Allowed Formats: <span class="text-white">JPEG, PNG, WEBP</span></p>
                                    <p class="text-xs text-white/40 italic">Max size: 6MB. Optimal dimensions: 1700x330</p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <label class="cursor-pointer bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-3 rounded-xl transition-all flex items-center gap-2 group">
                                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">cloud_upload</span>
                                        <span class="text-sm font-bold text-white">Choose Banner</span>
                                        <input type="file" wire:model="banner" class="hidden" accept="image/*">
                                    </label>

                                    @if($banner)
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="bg-primary hover:bg-primary-light text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                                            <span wire:loading.remove wire:target="saveBanner">Apply Banner</span>
                                            <span wire:loading wire:target="saveBanner" class="flex items-center gap-2">
                                                <div class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                                Uploading...
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @error('banner')
                                <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span>
                            @enderror

                            @if (session()->has('banner_success'))
                                <div class="p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-xs text-green-400 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                    {{ session('banner_success') }}
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- PROFILE COLOR (WIP) --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md opacity-70">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">palette</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Profile Color</h3>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @foreach(['#3db4f2', '#a060f9', '#74da64', '#f19520', '#e84242', '#f696be', '#677b94', 'linear-gradient(45deg, #7f13ec, #3db4f2)'] as $color)
                            <button 
                                wire:click="setProfileColor('{{ $color }}')"
                                class="w-10 h-10 rounded-lg border-2 transition-all relative group {{ $profile_color === $color ? 'border-white scale-110 shadow-lg' : 'border-white/10 hover:border-primary' }}"
                                style="background: {{ $color }}">
                                
                                @if($profile_color === $color)
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-[6px]">
                                        <span class="material-symbols-outlined text-white text-sm">check</span>
                                    </div>
                                @endif
                                
                                @if($loop->last && $profile_color !== $color)
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-[6px] group-hover:bg-transparent transition-colors">
                                        <span class="material-symbols-outlined text-white text-xs opacity-40">lock</span>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    @if (session()->has('profile_success'))
                        <div class="mt-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-xs text-green-400 font-bold flex items-center gap-2 animate-fade-in">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            {{ session('profile_success') }}
                        </div>
                    @endif
                </div>


                {{-- ABOUT / DESCRIPTION --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">description</span>
                            <h3 class="text-xl font-bold text-white uppercase tracking-tight">About</h3>
                        </div>
                        <span class="text-[10px] font-bold text-white/20 uppercase tracking-widest">Supports Markdown</span>
                    </div>

                    <form wire:submit.prevent="saveAbout" class="space-y-4">
                        <div class="space-y-0">
                            {{-- Toolbar --}}
                            <div class="flex flex-wrap gap-4 px-4 py-3 bg-surface-darker/50 rounded-t-xl border-x border-t border-white/10">
                                @foreach(['format_bold', 'format_italic', 'strikethrough_s', 'link', 'image', 'video_library', 'format_list_bulleted', 'format_quote', 'code'] as $tool)
                                    <span class="material-symbols-outlined text-white/30 text-lg cursor-help hover:text-primary transition-colors">{{ $tool }}</span>
                                @endforeach
                            </div>
                            <textarea 
                                wire:model.live.debounce.500ms="about"
                                placeholder="A little bit about yourself... (Markdown supported)"
                                class="w-full h-48 bg-surface-darker/30 border border-white/10 rounded-b-xl py-4 px-6 text-sm text-white focus:outline-none focus:border-primary/50 transition-all resize-none placeholder:text-white/20"></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                                @error('about')
                                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                                @enderror
                                @if (session()->has('about_success'))
                                    <div class="p-2 bg-green-500/10 border border-green-500/20 rounded-lg text-xs text-green-400 font-bold flex items-center gap-2 animate-fade-in">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        {{ session('about_success') }}
                                    </div>
                                @endif
                                @if (session()->has('about_error'))
                                    <div class="p-2 bg-red-500/10 border border-red-500/20 rounded-lg text-xs text-red-400 font-bold flex items-center gap-2 animate-fade-in">
                                        <span class="material-symbols-outlined text-sm">error</span>
                                        {{ session('about_error') }}
                                    </div>
                                @endif
                            </div>

                            <button type="submit" wire:loading.attr="disabled"
                                class="bg-primary hover:bg-primary-light text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                                <span wire:loading.remove wire:target="saveAbout">Save Description</span>
                                <span wire:loading wire:target="saveAbout" class="flex items-center gap-2">
                                    <div class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($activeTab === 'account')
            {{-- Account Settings --}}
            <div class="flex flex-col gap-6 animate-fade-in">
                <h2 class="text-2xl font-black text-white px-2">Account</h2>

                {{-- User Info (Placeholder/Mock) --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-primary">person</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Basic Info</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-white/40 ml-1 tracking-widest">Username</label>
                            <input type="text" value="{{ $user->name }}" readonly
                                class="w-full bg-surface-darker border border-white/10 rounded-xl py-3 px-4 text-sm text-white/60 focus:outline-none opacity-80 cursor-not-allowed">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-white/40 ml-1 tracking-widest">Email Address</label>
                            <input type="email" value="{{ $user->email }}" readonly
                                class="w-full bg-surface-darker border border-white/10 rounded-xl py-3 px-4 text-sm text-white/60 focus:outline-none opacity-80 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                {{-- Password Section (Placeholder) --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">lock</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Change Password</h3>
                    </div>
                    
                    <div class="flex flex-col items-center justify-center py-8 text-center gap-4">
                        <div class="bg-primary/10 p-4 rounded-full">
                            <span class="material-symbols-outlined text-primary text-4xl">construction</span>
                        </div>
                        <div>
                            <p class="text-white font-bold mb-1 italic">Password management is temporarily disabled.</p>
                            <p class="text-sm text-white/40">Work in Progress (WIP)</p>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'site-settings')
            {{-- Site Settings --}}
            <div class="flex flex-col gap-6 animate-fade-in">
                <h2 class="text-2xl font-black text-white px-2">Site Settings</h2>

                {{-- SCORE FORMAT CARD --}}
                <div class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Scoring Preferences</h2>
                    </div>

                    <form wire:submit="saveScoreFormat" class="space-y-6">
                        <div class="relative group">
                            <label for="select-score-format" class="block text-[10px] uppercase font-black text-white/40 mb-2 ml-1 tracking-widest group-focus-within:text-primary transition-colors">
                                Local Scoring System
                            </label>
                            <div class="relative">
                                <select id="select-score-format" wire:model="score_format" required
                                    class="w-full bg-surface-darker border border-white/10 rounded-xl py-4 pl-4 pr-12 text-sm text-white focus:outline-none focus:border-primary/50 transition-all appearance-none cursor-pointer hover:bg-surface-darker/80">
                                    @foreach ($score_formats as $item)
                                        <option value="{{ $item['value'] }}">
                                            {{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-white/30 pointer-events-none group-focus-within:text-primary transition-colors">
                                    expand_more
                                </span>
                            </div>
                            <p class="mt-4 text-xs text-white/40 leading-relaxed italic max-w-lg">
                                This preference determines how you input ratings and how scores are displayed to you. 
                                Changes are applied globally to all themes instantly.
                            </p>
                        </div>

                        <div class="pt-6 border-t border-white/5">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full bg-primary hover:bg-primary-light text-white text-xs font-black uppercase py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-3">
                                <span wire:loading.remove wire:target="saveScoreFormat" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                                    Apply Global Preference
                                </span>
                                <span wire:loading wire:target="saveScoreFormat" class="flex items-center gap-2">
                                    <div class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                    Updating...
                                </span>
                            </button>

                            @if (session()->has('settings_success'))
                                <div class="mt-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-xs text-green-400 font-bold flex items-center gap-3 animate-pulse">
                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                    {{ session('settings_success') }}
                                </div>
                            @endif
                </div>

                {{-- SITE THEME --}}
                <div x-data="{ 
                    currentTheme: localStorage.getItem('theme') || 'dark',
                    setTheme(theme) {
                        this.currentTheme = theme;
                        localStorage.setItem('theme', theme);
                        if (theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }" class="bg-surface-dark/30 p-8 rounded-2xl border border-white/5 shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">contrast</span>
                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">Site Theme</h3>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach(['Light' => 'light_mode', 'Dark' => 'dark_mode', 'Contrast' => 'contrast', 'Auto' => 'brightness_auto'] as $name => $icon)
                            @php
                                $isAvailable = in_array($name, ['Light', 'Dark']);
                                $themeKey = strtolower($name);
                            @endphp
                            <button 
                                @if($isAvailable)
                                    @click="setTheme('{{ $themeKey }}')"
                                @endif
                                :class="{ 
                                    'border-primary bg-primary/10': currentTheme === '{{ $themeKey }}',
                                    'border-white/5 bg-surface-darker/50': currentTheme !== '{{ $themeKey }}',
                                    'opacity-50 cursor-not-allowed': !{{ $isAvailable ? 'true' : 'false' }},
                                    'hover:border-primary/50': {{ $isAvailable ? 'true' : 'false' }}
                                }"
                                class="p-4 rounded-xl border flex flex-col items-center gap-3 transition-all group">
                                <span class="material-symbols-outlined text-3xl transition-transform group-hover:scale-110"
                                    :class="currentTheme === '{{ $themeKey }}' ? 'text-primary' : 'text-white/40'">
                                    {{ $icon }}
                                </span>
                                <span class="text-[10px] font-black uppercase tracking-widest"
                                    :class="currentTheme === '{{ $themeKey }}' ? 'text-white' : 'text-white/60'">
                                    {{ $name }}
                                </span>
                                
                                @if(!$isAvailable)
                                    <span class="absolute top-2 right-2 text-[8px] font-bold text-primary/40 uppercase tracking-tighter">WIP</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            {{-- WIP Placeholders for other tabs --}}
            <div class="flex flex-col gap-6 animate-fade-in">
                <h2 class="text-2xl font-black text-white px-2 uppercase tracking-tighter">{{ str_replace('-', ' ', $activeTab) }}</h2>
                
                <div class="bg-surface-dark/30 p-12 rounded-3xl border border-white/5 shadow-2xl backdrop-blur-md flex flex-col items-center justify-center text-center gap-6 min-h-[400px]">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-primary/20 blur-2xl rounded-full animate-pulse"></div>
                        <div class="relative bg-surface rounded-full p-8 border border-white/5 shadow-inner">
                            <span class="material-symbols-outlined text-primary text-6xl">engineering</span>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-black text-white mb-2 tracking-tight">Section Under Construction</h3>
                        <p class="text-white/40 max-w-sm mx-auto">
                            We are currently building the <span class="text-primary font-bold italic">{{ str_replace('-', ' ', $activeTab) }}</span> features. Stay tuned for upcoming updates!
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <style>
        .animate-fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>
