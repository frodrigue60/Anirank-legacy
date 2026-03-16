@section('title', __('Notifications') . ' | ' . config('app.name'))
@section('description', __('Manage your notifications and stays updated with the community.'))

<div class="max-w-[1440px] mx-auto py-10 px-6">
    <div class="flex flex-col lg:flex-row gap-10">
        {{-- Sidebar Categories --}}
        <aside class="w-full lg:w-64 shrink-0">
            <div class="flex items-center justify-between mb-6 px-4">
                <h2 class="text-xs font-bold text-white/20 uppercase tracking-[0.2em]">{{ __('Notifications') }}</h2>
                <button class="text-white/20 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[18px]">settings</span>
                </button>
            </div>

            <nav class="space-y-1">
                @php
                    $categories = [
                        ['id' => 'all', 'label' => __('All'), 'icon' => 'list', 'enabled' => true],
                        ['id' => 'airing', 'label' => __('Airing'), 'icon' => 'schedule', 'enabled' => false],
                        ['id' => 'activity', 'label' => __('Activity'), 'icon' => 'local_activity', 'enabled' => true],
                        ['id' => 'forum', 'label' => __('Forum'), 'icon' => 'forum', 'enabled' => true],
                        ['id' => 'follow', 'label' => __('Follows'), 'icon' => 'person_add', 'enabled' => true],
                        ['id' => 'media', 'label' => __('Media'), 'icon' => 'movie', 'enabled' => false],
                    ];
                @endphp

                @foreach($categories as $cat)
                    <button 
                        wire:click="{{ $cat['enabled'] ? "setCategory('{$cat['id']}')" : "" }}"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all group {{ !$cat['enabled'] ? 'opacity-30 cursor-not-allowed' : '' }} {{ $activeCategory === $cat['id'] ? 'bg-primary/10 text-primary' : 'text-white/40 hover:text-white hover:bg-white/5' }}"
                    >
                        <span class="material-symbols-outlined text-[20px] {{ $activeCategory === $cat['id'] ? 'filled' : '' }} group-hover:scale-110 transition-transform">
                            {{ $cat['icon'] }}
                        </span>
                        {{ $cat['label'] }}
                    </button>
                @endforeach
            </nav>

            <div class="mt-10 px-4">
                <button wire:click="markAllAsRead" 
                    class="w-full py-3 px-4 rounded-xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-light hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                    {{ Auth::user()->unreadNotifications()->count() === 0 ? 'disabled' : '' }}>
                    {{ __('Mark all as read') }}
                </button>
            </div>
        </aside>

        {{-- Main Content - List --}}
        <div class="flex-1">
            <div class="space-y-3">
                @forelse($notifications as $notification)
                    <div wire:key="notif-{{ $notification->id }}" 
                        class="group relative flex items-center gap-4 p-4 rounded-2xl bg-surface-darker/30 border border-white/5 hover:border-white/10 hover:bg-surface-darker/50 transition-all {{ !$notification->read_at ? 'ring-1 ring-primary/20 border-primary/20 bg-primary/5' : '' }}">
                        
                        {{-- Thumbnail --}}
                        <div class="relative shrink-0 w-20 h-14 rounded-lg overflow-hidden bg-surface-dark shadow-xl">
                            @if($notification->type === 'follow')
                                <x-ui.image :src="$notification->data['follower_avatar'] ?? null" 
                                    :fallback="$notification->subject?->avatar_url"
                                    alt="Avatar" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-white text-[20px] filled">person</span>
                                </div>
                            @elseif($notification->type === 'reply')
                                <x-ui.image :src="$notification->data['replier_avatar'] ?? null" 
                                    alt="Avatar" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-white text-[20px] filled">reply</span>
                                </div>
                            @elseif($notification->type === 'activity')
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-primary text-[24px] filled">notifications_active</span>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-surface-dark">
                                    <span class="material-symbols-outlined text-white/10">notifications</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-white/90 leading-snug">
                                @if($notification->type === 'follow')
                                    <span class="text-white font-bold">{{ $notification->data['follower_name'] ?? __('Someone') }}</span>
                                    {{ __('started following you.') }}
                                @elseif($notification->type === 'reply')
                                    <span class="text-white font-bold">{{ $notification->data['replier_name'] ?? __('Someone') }}</span>
                                    {{ __('replied to your comment:') }}
                                    <span class="text-white/40 italic block mt-1 text-xs">"{{ $notification->data['comment_content'] ?? '' }}"</span>
                                @elseif($notification->type === 'activity')
                                    {{ $notification->data['message'] ?? __('New update available') }}
                                @else
                                    {{ $notification->data['message'] ?? __('New update available') }}
                                @endif
                            </h3>
                        </div>

                        {{-- Meta & Actions --}}
                        <div class="flex flex-col items-end gap-2 text-right shrink-0">
                            <span class="text-[11px] font-bold text-white/20 uppercase tracking-wider">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                        <div class="flex items-center gap-2 transition-opacity">
                                @if(!$notification->read_at)
                                    <button wire:click="markAsRead('{{ $notification->id }}')" 
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-[18px]">check</span>
                                    </button>
                                @endif
                                <button wire:click="delete('{{ $notification->id }}')" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-white/20 hover:bg-red-500/10 hover:text-red-500 transition-all">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>

                        {{-- Unread Indicator --}}
                        @if(!$notification->read_at)
                            <div class="absolute -left-1.5 top-1/2 -translate-y-1/2 w-3 h-3 bg-primary rounded-full border-2 border-background-dark shadow-[0_0_12px_rgba(127,19,236,0.6)]"></div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-32 text-center glass-panel rounded-3xl border border-white/5">
                        <div class="w-24 h-24 rounded-full bg-surface-darker/50 flex items-center justify-center mb-6 shadow-2xl border border-white/5 group">
                            <span class="material-symbols-outlined text-white/5 text-6xl group-hover:scale-110 group-hover:text-primary/20 transition-all duration-500">notifications_off</span>
                        </div>
                        <h3 class="text-white font-black text-xl mb-2">{{ __('Nothing to see here') }}</h3>
                        <p class="text-white/30 text-sm max-w-xs mx-auto leading-relaxed">
                            {{ __('Your notifications for') }} <span class="text-primary font-bold">{{ $activeCategory === 'all' ? __('all categories') : str($activeCategory)->headline() }}</span> {{ __('will appear here.') }}
                        </p>
                    </div>
                @endforelse

                @if($notifications->hasPages())
                    <div class="mt-10">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
