<div class="bg-surface-darker rounded-2xl border border-white/5 overflow-hidden flex flex-col h-full">
    <div class="p-4 md:p-6 border-b border-white/5 flex items-center justify-between bg-surface-dark/30">
        <h3 class="font-bold text-white text-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">dynamic_feed</span>
            Community Activity
        </h3>
    </div>
    <div class="flex flex-col">
        @forelse ($activities as $act)
            @php
                $user = $act->user;
                $target = $act->target;

                // Determine icons and colors based on action type
                $icon = 'info';
                $iconColor = 'text-white/50';
                $iconBg = 'bg-white/5';
                $actionText = 'interacted with';

                if ($act->action_type === 'favorite') {
                    $icon = 'favorite';
                    $iconColor = 'text-red-400';
                    $iconBg = 'bg-red-400/10';
                    $actionText = 'favorited';
                } elseif ($act->action_type === 'rating') {
                    $icon = 'star';
                    $iconColor = 'text-yellow-400';
                    $iconBg = 'bg-yellow-400/10';
                    $actionText = 'rated';
                } elseif ($act->action_type === 'comment') {
                    $icon = 'chat_bubble';
                    $iconColor = 'text-blue-400';
                    $iconBg = 'bg-blue-400/10';
                    $actionText = 'commented on';
                }

                // Determine target name and URL
                $targetName = 'Unknown';
                $targetUrl = '#';
                $animeTitle = '';
                $thumbnailUrl = null;

                if (class_basename($act->target_type) === 'Song') {
                    $targetName = $target->name;
                    $animeTitle = $target->anime->title ?? '';
                    $thumbnailUrl = $target->anime->thumbnail_url ?? null;
                    $targetUrl = route('songs.show.nested', ['anime' => $target->anime->slug, 'song' => $target->slug]);
                } elseif (class_basename($act->target_type) === 'SongVariant') {
                    $targetName = $target->song->name . ' (' . strtoupper($target->slug) . ')';
                    $animeTitle = $target->song->anime->title ?? '';
                    $thumbnailUrl = $target->song->anime->thumbnail_url ?? null;
                    $targetUrl = route('variants.show', [
                        'anime' => $target->song->anime->slug,
                        'song' => $target->song->slug,
                        'variant' => $target->slug,
                    ]);
                }
            @endphp

            <div class="flex gap-4 p-4 border-b border-white/5 hover:bg-white/[0.02] transition-colors relative group">
                {{-- User Avatar --}}
                <a href="{{ route('users.show', $user->slug ?? $user->id) }}"
                    class="relative shrink-0 w-10 h-10 rounded-full border border-white/10 overflow-hidden mt-1 group-hover:border-primary transition-colors">
                    <x-ui.image :src="$user->avatar_url" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                </a>

                {{-- Activity Content --}}
                <div class="flex-1 min-w-0 flex flex-col gap-1 pr-6">
                    <div class="text-sm text-white/80 leading-tight">
                        <a href="{{ route('users.show', $user->slug ?? $user->id) }}"
                            class="font-bold text-white hover:text-primary transition-colors">{{ $user->name }}</a>
                        <span class="text-white/50">{{ $actionText }}</span>
                    </div>

                    {{-- Target Card Inside Feed --}}
                    <div
                        class="mt-2 p-2 rounded-lg bg-black/20 border border-white/5 flex gap-3 items-center group/card hover:bg-black/40 transition-colors">
                        @if ($thumbnailUrl)
                            <div class="w-12 h-12 shrink-0 rounded overflow-hidden relative">
                                <x-ui.image :src="$thumbnailUrl" alt="Thumbnail" class="w-full h-full object-cover" />
                                <div
                                    class="absolute inset-0 bg-black/20 group-hover/card:bg-transparent transition-colors">
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-12 shrink-0 rounded bg-white/5 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white/20">music_note</span>
                            </div>
                        @endif

                        <div class="flex flex-col min-w-0 pr-2">
                            <a href="{{ $targetUrl }}"
                                class="font-bold text-sm text-white truncate hover:underline hover:text-primary decoration-primary/50 underline-offset-2">
                                {{ $targetName }}
                            </a>
                            <p class="text-xs text-primary truncate">{{ $animeTitle }}</p>
                        </div>
                    </div>

                    {{-- Action Value Details (Rating Score / Comment Text) --}}
                    @if ($act->action_type === 'rating' && $act->action_value)
                        <div class="mt-2 flex items-center gap-1.5 text-yellow-500 font-bold text-sm">
                            <span class="material-symbols-outlined filled text-md">star</span>
                            <span>Give it a {{ $act->action_value }}/100</span>
                        </div>
                    @elseif ($act->action_type === 'comment' && $act->action_value)
                        <div
                            class="mt-2 text-sm text-white/70 italic border-l-2 border-white/10 pl-3 py-1 line-clamp-2">
                            "{{ $act->action_value }}"
                        </div>
                    @endif

                    {{-- Timestamp --}}
                    <div
                        class="text-[11px] text-white/40 mt-2 font-medium uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full {{ $iconBg }} block"></span>
                        {{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }}
                    </div>
                </div>

                {{-- Floating Right Icon --}}
                {{-- <div
                    class="absolute top-4 right-4 w-7 h-7 rounded-full flex items-center justify-center {{ $iconBg }} {{ $iconColor }}">
                    <span class="material-symbols-outlined filled text-[14px]">{{ $icon }}</span>
                </div> --}}
            </div>
        @empty
            <div class="p-10 text-center flex flex-col items-center gap-3 text-white/50">
                <span class="material-symbols-outlined text-4xl opacity-50">history_toggle_off</span>
                <p class="text-sm">No recent community activity yet.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }
</style>
