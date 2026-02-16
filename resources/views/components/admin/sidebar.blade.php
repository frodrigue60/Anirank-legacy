<aside
    class="fixed top-0 left-0 h-screen bg-zinc-900 border-r border-zinc-800 transition-all duration-300 flex flex-col z-50 shadow-2xl"
    :class="sidebarOpen ? 'w-64' : 'w-20'" x-cloak>

    {{-- Logo / Branding --}}
    <div class="h-16 flex items-center px-6 border-b border-zinc-800 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div
                class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-primary/20">
                <span class="text-white font-black text-xs">AR</span>
            </div>
            <span class="font-black text-lg tracking-tighter whitespace-nowrap transition-opacity duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                ANIRANK</span>
            </span>
        </a>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-1 custom-scrollbar">
        <div class="space-y-4">
            {{-- Main Section --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-widest transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 invisible'">
                    Main Menu
                </p>
                <div class="space-y-1">
                    <x-admin.sidebar-link route="admin.dashboard" icon="dashboard" label="Dashboard" />
                    <x-admin.sidebar-link route="admin.posts.index" icon="movie" label="Animes" />
                    <x-admin.sidebar-link route="admin.songs.index" icon="music_note" label="Songs" />
                    <x-admin.sidebar-link route="admin.artists.index" icon="mic" label="Artists" />
                    <x-admin.sidebar-link route="admin.users.index" icon="group" label="Users" />
                </div>
            </div>

            {{-- Metadata Section --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-widest transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 invisible'">
                    Metadata
                </p>
                <div class="space-y-1">
                    <x-admin.sidebar-link route="admin.producers.index" icon="factory" label="Producers" />
                    <x-admin.sidebar-link route="admin.studios.index" icon="business" label="Studios" />
                    <x-admin.sidebar-link route="admin.years.index" icon="calendar_today" label="Years" />
                    <x-admin.sidebar-link route="admin.seasons.index" icon="ac_unit" label="Seasons" />
                </div>
            </div>

            {{-- System Section --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-widest transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 invisible'">
                    Reports & Logs
                </p>
                <div class="space-y-1">
                    <x-admin.sidebar-link route="admin.reports.index" icon="flag" label="Reports" />
                    <x-admin.sidebar-link route="admin.requests.index" icon="mail" label="Requests" />
                </div>
            </div>
        </div>
    </nav>

    {{-- Footer Actions --}}
    <div class="p-3 border-t border-zinc-800 space-y-1 shrink-0 bg-zinc-900/50 backdrop-blur-md">
        <a href="{{ url('/') }}"
            class="flex items-center gap-4 px-3 py-2 text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-xl transition-all group overflow-hidden"
            title="Back to Site">
            <span
                class="material-symbols-outlined shrink-0 group-hover:scale-110 transition-transform">open_in_new</span>
            <span class="text-sm font-bold whitespace-nowrap transition-opacity duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                BACK TO SITE
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-4 px-3 py-2 text-zinc-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all group overflow-hidden"
                title="Logout">
                <span
                    class="material-symbols-outlined shrink-0 group-hover:scale-110 transition-transform">logout</span>
                <span class="text-sm font-bold whitespace-nowrap transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                    LOGOUT
                </span>
            </button>
        </form>
    </div>
</aside>
