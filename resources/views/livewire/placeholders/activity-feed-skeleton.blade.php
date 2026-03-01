<div class="bg-surface-darker rounded-2xl border border-white/5 overflow-hidden flex flex-col h-full animate-pulse">
    <div class="p-4 md:p-6 border-b border-white/5 flex items-center justify-between bg-surface-dark/30">
        <div class="h-6 w-48 bg-white/10 rounded"></div>
    </div>
    <div class="flex flex-col max-h-[600px] overflow-hidden">
        @for ($i = 0; $i < 6; $i++)
            <div class="flex gap-4 p-4 border-b border-white/5 relative">
                {{-- Avatar Skeleton --}}
                <div class="shrink-0 w-10 h-10 rounded-full bg-white/10 mt-1"></div>

                {{-- Content Skeleton --}}
                <div class="flex-1 min-w-0 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-24 bg-white/10 rounded"></div>
                        <div class="h-4 w-16 bg-white/5 rounded"></div>
                    </div>

                    {{-- Target Card Skeleton --}}
                    <div class="mt-1 p-2 rounded-lg bg-black/20 border border-white/5 flex gap-3 items-center">
                        <div class="w-12 h-12 shrink-0 rounded bg-white/10"></div>
                        <div class="flex flex-col gap-2 flex-1">
                            <div class="h-4 w-3/4 bg-white/10 rounded"></div>
                            <div class="h-3 w-1/2 bg-white/5 rounded"></div>
                        </div>
                    </div>

                    {{-- Timestamp Skeleton --}}
                    <div class="h-3 w-20 bg-white/5 rounded mt-2"></div>
                </div>

                {{-- Floating Right Icon Skeleton --}}
                <div class="absolute top-4 right-4 w-7 h-7 rounded-full bg-white/10"></div>
            </div>
        @endfor
    </div>
</div>
