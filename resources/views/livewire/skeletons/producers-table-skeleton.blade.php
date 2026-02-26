<div class="animate-pulse flex flex-col gap-8">
    {{-- Header Skeleton --}}
    <div class="mb-4">
        <div class="h-8 w-64 bg-surface-darker rounded mb-2"></div>
        <div class="h-1 w-20 bg-primary/30 rounded-full"></div>
    </div>
    {{-- Filters Bar Skeleton --}}
    <div class="bg-surface-dark/30 p-4 rounded-xl border border-white/5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="h-10 bg-surface-darker rounded-lg col-span-1"></div>
            <div class="h-10 bg-surface-darker rounded-lg col-span-1"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for ($i = 0; $i < 9; $i++)
            <div class="aspect-16/10 bg-surface-darker rounded-xl border border-white/5 relative overflow-hidden">
                <div class="absolute bottom-0 left-0 right-0 p-6 space-y-3">
                    <div class="h-6 w-3/4 bg-surface-dark rounded"></div>
                    <div class="pt-4 border-t border-white/10 flex justify-between">
                        <div class="h-8 w-16 bg-surface-dark rounded"></div>
                        <div class="h-8 w-16 bg-surface-dark rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
