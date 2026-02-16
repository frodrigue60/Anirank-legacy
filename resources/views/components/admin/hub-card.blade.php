@props(['route', 'icon', 'label', 'description'])

<a href="{{ route($route) }}"
    class="group p-6 bg-zinc-900 border border-zinc-800 rounded-3xl hover:bg-zinc-800/50 hover:border-primary/50 transition-all hover:scale-[1.02] active:scale-95 shadow-xl relative overflow-hidden">

    <div class="absolute -right-8 -bottom-8 opacity-0 group-hover:opacity-10 transition-opacity duration-500">
        <span class="material-symbols-outlined text-9xl! text-primary">
            {{ $icon }}
        </span>
    </div>

    <div class="flex flex-col gap-6 relative z-10">
        <div class="flex items-center justify-between">
            <div
                class="w-14 h-14 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400 border border-zinc-700 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all shadow-lg">
                <span class="material-symbols-outlined text-2xl!">
                    {{ $icon }}
                </span>
            </div>
            <span
                class="material-symbols-outlined text-zinc-700 group-hover:text-primary group-hover:translate-x-1 transition-all">
                arrow_forward_ios
            </span>
        </div>

        <div class="space-y-2">
            <h3
                class="text-xl font-bold text-white tracking-tight group-hover:text-primary transition-colors uppercase">
                {{ $label }}</h3>
            <p class="text-sm text-zinc-500 leading-relaxed font-medium">
                {{ $description }}
            </p>
        </div>
    </div>
</a>
