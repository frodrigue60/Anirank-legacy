<div class="max-w-[1440px] mx-auto px-6 py-12 animate-pulse">
    <div class="mb-4">
        <div class="h-8 w-64 bg-surface-darker rounded mb-2"></div>
        <div class="h-1 w-20 bg-primary/30 rounded-full"></div>
    </div>
    <div class="flex flex-col gap-10">
        {{-- Search & Filters Skeleton --}}
        <div class="flex flex-wrap items-center justify-center gap-4">
            <div class="relative flex-1 min-w-[300px]">
                <div class="h-3 w-12 bg-surface-darker rounded mb-1.5 ml-1"></div>
                <div class="h-11 bg-surface-darker/50 border border-white/10 rounded-xl"></div>
            </div>
            <div class="relative min-w-[200px]">
                <div class="h-3 w-20 bg-surface-darker rounded mb-1.5 ml-1"></div>
                <div class="h-11 bg-surface-darker border-white/10 rounded-xl"></div>
            </div>
        </div>

        {{-- Artists Grid Skeleton --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-8 mt-8">
            @for ($i = 0; $i < 18; $i++)
                <div class="flex flex-col items-center gap-4">
                    <div class="w-full aspect-square rounded-full bg-surface-darker border border-white/5"></div>
                    <div class="flex flex-col items-center gap-2 w-full">
                        <div class="h-5 w-24 bg-surface-darker rounded"></div>
                        <div class="h-6 w-20 bg-surface-darker rounded-full border border-white/5"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
