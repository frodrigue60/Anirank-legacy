<div class="max-w-[1440px] mx-auto px-4 md:px-8 py-8 animate-pulse">
    {{-- Header & Filters Skeleton --}}
    <div class="flex flex-col gap-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <div class="h-8 w-48 bg-surface-darker rounded mb-2"></div>
                <div class="h-1 w-20 bg-primary/30 rounded-full"></div>
            </div>
            <div class="h-10 w-32 bg-surface-darker rounded-lg"></div>
        </div>

        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 bg-surface-dark/30 p-4 rounded-xl border border-white/5">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-10 bg-surface-darker rounded-lg"></div>
            @endfor
        </div>
    </div>

    {{-- Content Skeleton --}}
    <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        @for ($i = 0; $i < 12; $i++)
            <div class="aspect-2/3 bg-surface-darker rounded-lg"></div>
        @endfor
    </div>
</div>
