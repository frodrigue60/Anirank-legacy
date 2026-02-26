<div class="max-w-[1440px] mx-auto px-4 md:px-8 py-8 animate-pulse">
    {{-- Header & Filters Skeleton --}}
    <div class="flex flex-col gap-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <div class="h-8 w-48 bg-surface-darker rounded mb-2"></div>
                <div class="h-1 w-20 bg-primary/30 rounded-full"></div>
            </div>
        </div>

        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 bg-surface-dark/30 p-4 rounded-xl border border-white/5">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-10 bg-surface-darker rounded-lg"></div>
            @endfor
        </div>
    </div>

    {{-- Content Skeleton (Grid) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
        @for ($i = 0; $i < 10; $i++)
            <div
                class="group relative overflow-hidden rounded-xl h-48 border border-white/5 bg-surface-dark/30 flex items-center justify-between p-6">
                <div class="space-y-3 w-2/3">
                    <div class="h-4 w-16 bg-surface-darker rounded"></div>
                    <div class="h-6 w-3/4 bg-surface-darker rounded"></div>
                    <div class="h-4 w-1/2 bg-surface-darker rounded"></div>
                </div>
                <div class="flex flex-col items-end gap-3 shrink-0">
                    <div class="h-10 w-20 bg-surface-darker rounded-lg"></div>
                    <div class="h-10 w-10 rounded-full bg-surface-darker"></div>
                </div>
            </div>
        @endfor
    </div>
</div>
