@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route . '*');
@endphp

<a href="{{ route($route) }}"
    class="flex items-center gap-4 px-3 py-2.5 rounded-xl transition-all group relative overflow-hidden"
    :class="{{ $isActive ? 'true' : 'false' }}
        ?
        'bg-primary text-white shadow-lg shadow-primary/20' :
        'text-zinc-400 hover:text-white hover:bg-zinc-800/50'"
    title="{{ $label }}">

    <span
        class="material-symbols-outlined shrink-0 group-hover:scale-110 transition-transform {{ $isActive ? 'filled' : '' }}"
        style="font-size: 20px;">
        {{ $icon }}
    </span>

    <span class="text-xs font-black tracking-widest uppercase whitespace-nowrap transition-opacity duration-300"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'">
        {{ $label }}
    </span>

    {{-- Active Indicator for Collapsed State --}}
    <template x-if="!sidebarOpen && {{ $isActive ? 'true' : 'false' }}">
        <div class="absolute inset-y-2 left-0 w-1 bg-white rounded-r-full"></div>
    </template>
</a>
