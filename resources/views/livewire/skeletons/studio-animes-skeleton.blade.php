<div class="animate-pulse space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div class="space-y-2">
            <div class="h-8 w-64 bg-surface-darker rounded"></div>
            <div class="h-1 w-20 bg-primary/20 rounded-full"></div>
        </div>
        <div class="h-10 w-32 bg-surface-darker rounded-lg"></div>
    </div>
    <div class="bg-surface-dark/30 p-4 rounded-xl border border-white/5 h-20 w-full"></div>
    <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        @for ($i = 0; $i < 12; $i++)
            <div class="space-y-2">
                <div class="aspect-2/3 bg-surface-darker rounded-lg"></div>
                <div class="h-4 bg-surface-darker rounded w-3/4"></div>
            </div>
        @endfor
    </div>
</div>
