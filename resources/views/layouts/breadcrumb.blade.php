<nav aria-label="breadcrumb"
    class="bg-surface-dark/40 backdrop-blur-xl border border-white/5 rounded-2xl px-5 py-2.5 inline-flex shadow-xl shadow-black/20">
    <ol class="flex items-center gap-2.5 text-[10px] font-black tracking-[0.2em] uppercase">
        <li class="flex items-center">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center text-zinc-400 hover:text-primary transition-colors group">
                <span
                    class="material-symbols-outlined text-lg! mr-2 group-hover:scale-110 transition-transform">home</span>
                <span class="hidden sm:inline">HOME</span>
            </a>
        </li>

        @isset($breadcrumb)
            @foreach ($breadcrumb as $item)
                <li class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-white/10 text-base!">chevron_right</span>
                    @if (!$loop->last)
                        <a href="{{ $item['url'] }}" class="text-zinc-400 hover:text-primary transition-colors">
                            {{ $item['name'] }}
                        </a>
                    @else
                        <span class="text-white truncate max-w-[150px] sm:max-w-none">
                            {{ $item['name'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        @endisset
    </ol>
</nav>
