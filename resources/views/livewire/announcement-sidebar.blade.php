<div>
    @if ($announcements->isNotEmpty())
        <div class="space-y-4 mb-8" wire:loading.class="opacity-50 pointer-events-none">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-2 px-2">
                <span class="material-symbols-outlined text-lg">campaign</span>
                {{ __('Announcements') }}
            </h3>

            <div class="space-y-3">
                @foreach ($announcements as $announcement)
                    @php
                        $hasImage = $announcement->image_url;
                        $typeColors = [
                            'info' => 'border-primary/30',
                            'success' => 'border-green-500/30',
                            'warning' => 'border-amber-500/30',
                            'danger' => 'border-red-500/30',
                            'event' => 'border-purple-500/50 hero-glow',
                        ];
                        $borderColor = $typeColors[$announcement->type] ?? $typeColors['info'];
                    @endphp

                    <div
                        class="aspect-21/7! relative group overflow-hidden rounded-2xl border {{ $borderColor }} glass-panel transition-all duration-300 hover:scale-[1.02]">
                        {{-- Background Image with Overlay --}}
                        @if ($hasImage)
                            <div class="absolute inset-0 z-0">
                                <img src="{{ $announcement->image_url }}" alt=""
                                    class="w-full h-full object-cover opacity-30 group-hover:opacity-40 transition-opacity duration-300">
                                <div
                                    class="absolute inset-0 bg-linear-to-t from-background via-background/80 to-transparent">
                                </div>
                            </div>
                        @endif

                        {{-- Content --}}
                        <div class="relative z-10 p-4">
                            <div class="flex items-start gap-3">
                                @if ($announcement->icon)
                                    <div
                                        class="shrink-0 w-8 h-8 rounded-lg bg-surface-darker flex items-center justify-center border border-white/5">
                                        <span
                                            class="material-symbols-outlined text-primary">{{ $announcement->icon }}</span>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-sm text-gray-100 mb-1 line-clamp-1">
                                        {{ $announcement->title }}
                                    </h4>
                                    @if ($announcement->content)
                                        <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">
                                            {{ $announcement->content }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if ($announcement->url)
                                <a href="{{ $announcement->url }}"
                                    class="mt-3 py-2 px-3 rounded-xl hover:bg-primary/30 text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 transition-colors">
                                    {{ __('Go to') }}
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
