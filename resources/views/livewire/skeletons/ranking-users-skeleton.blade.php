<div class="animate-pulse flex flex-col gap-1">
    @for ($i = 0; $i < 15; $i++)
        <div class="grid grid-cols-[48px_1fr] sm:grid-cols-12 gap-x-4 gap-y-3 sm:gap-4 px-4 sm:px-8 py-4 sm:py-5 items-center border-b border-white/5 bg-surface-dark/10">
            {{-- Rank --}}
            <div class="sm:col-span-1 h-8 w-8 bg-white/5 rounded-full mx-auto"></div>
            
            {{-- User Info --}}
            <div class="sm:col-span-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/5"></div>
                <div class="flex-1">
                    <div class="h-4 bg-white/5 rounded w-32 mb-2"></div>
                    <div class="flex items-center gap-3 sm:hidden">
                        <div class="h-3 w-10 bg-white/5 rounded"></div>
                        <div class="h-3 w-10 bg-white/5 rounded"></div>
                        <div class="h-3 w-10 bg-white/5 rounded"></div>
                    </div>
                </div>
            </div>
            
            {{-- Level/XP --}}
            <div class="hidden sm:flex sm:col-span-2 flex-col items-center gap-2">
                <div class="h-5 w-16 bg-white/5 rounded"></div>
                <div class="h-3 w-12 bg-white/5 rounded"></div>
            </div>
            
            {{-- Ratings --}}
            <div class="hidden sm:block sm:col-span-2 h-6 w-12 bg-white/5 rounded mx-auto"></div>
            
            {{-- Comments --}}
            <div class="hidden sm:block sm:col-span-2 h-6 w-12 bg-white/5 rounded mx-auto"></div>
        </div>
    @endfor
</div>
