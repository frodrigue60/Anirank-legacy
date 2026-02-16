@props(['icon', 'color', 'label', 'value'])

@php
    $colors = [
        'blue' => 'from-blue-600/20 to-blue-600/5 text-blue-400 border-blue-500/20 shadow-blue-900/10',
        'purple' => 'from-purple-600/20 to-purple-600/5 text-purple-400 border-purple-500/20 shadow-purple-900/10',
        'emerald' =>
            'from-emerald-600/20 to-emerald-600/5 text-emerald-400 border-emerald-500/20 shadow-emerald-900/10',
        'amber' => 'from-amber-600/20 to-amber-600/5 text-amber-400 border-amber-500/20 shadow-amber-900/10',
        'rose' => 'from-rose-600/20 to-rose-600/5 text-rose-400 border-rose-500/20 shadow-rose-900/10',
        'cyan' => 'from-cyan-600/20 to-cyan-600/5 text-cyan-400 border-cyan-500/20 shadow-cyan-900/10',
        'indigo' => 'from-indigo-600/20 to-indigo-600/5 text-indigo-400 border-indigo-500/20 shadow-indigo-900/10',
    ];
    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<div
    class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-xl hover:border-zinc-700 transition-all group overflow-hidden relative">
    <div
        class="absolute -right-4 -top-4 w-24 h-24 bg-linear-to-br {{ $colorClass }} blur-3xl opacity-20 group-hover:opacity-40 transition-opacity">
    </div>

    <div class="flex flex-col gap-4 relative z-10">
        <div
            class="w-12 h-12 rounded-2xl bg-linear-to-br {{ $colorClass }} flex items-center justify-center border transition-transform group-hover:scale-110">
            <span class="material-symbols-outlined text-2xl!">
                {{ $icon }}
            </span>
        </div>

        <div class="space-y-1">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest leading-none">{{ $label }}
            </p>
            <h3 class="text-3xl font-black text-white tracking-tighter">{{ $value }}</h3>
        </div>
    </div>
</div>
