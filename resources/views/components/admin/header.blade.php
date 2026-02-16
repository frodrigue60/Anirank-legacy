<header
    class="h-16 bg-zinc-950/50 backdrop-blur-xl border-b border-zinc-800 flex items-center justify-between px-6 sticky top-0 z-40">
    {{-- Left Side: Toggle & Breadcrumb --}}
    <div class="flex items-center gap-6">
        <button @click="toggleSidebar()"
            class="w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-xl transition-all group">
            <span class="material-symbols-outlined group-hover:scale-110 transition-transform"
                x-text="sidebarOpen ? 'menu_open' : 'menu'">
                menu_open
            </span>
        </button>

        <div class="hidden md:block">
            @include('layouts.breadcrumb')
        </div>
    </div>

    {{-- Right Side: Search & User --}}
    <div class="flex items-center gap-4">
        {{-- Quick Search --}}
        <div class="hidden lg:flex items-center relative group">
            <span
                class="material-symbols-outlined absolute left-3 text-zinc-500 text-lg group-focus-within:text-primary transition-colors">search</span>
            <input type="text" placeholder="Quick search..."
                class="bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs rounded-xl py-2 pl-10 pr-4 w-64 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all placeholder:text-zinc-600">
            <div class="absolute right-3 hidden group-focus-within:flex items-center gap-1">
                <kbd
                    class="px-1.5 py-0.5 bg-zinc-800 border border-zinc-700 rounded text-[10px] text-zinc-500 font-sans">ESC</kbd>
            </div>
        </div>

        {{-- Divider --}}
        <div class="h-8 w-px bg-zinc-800 mx-2 hidden sm:block"></div>

        {{-- User Info --}}
        <div class="flex items-center gap-3 pl-2">
            <div class="hidden sm:flex flex-col items-end text-right">
                <span
                    class="text-xs font-black text-white leading-tight uppercase tracking-widest">{{ Auth::user()->name }}</span>
                <span
                    class="text-[10px] font-bold text-primary uppercase tracking-tighter">{{ Auth::user()->role ?? 'ADMINISTRATOR' }}</span>
            </div>
            <div class="w-10 h-10 border-2 border-zinc-800 rounded-xl overflow-hidden shadow-lg">
                @if (Auth::user()->image)
                    <img src="{{ Storage::url(Auth::user()->image) }}" alt="Avatar"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-zinc-800 flex items-center justify-center text-zinc-500">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
