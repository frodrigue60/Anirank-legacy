<div class="relative">
    <a href="{{ route('notifications.index') }}" 
       class="flex items-center justify-center w-10 h-10 rounded-full bg-surface-darker/50 border border-white/10 text-white/60 hover:text-primary hover:border-primary/50 transition-all group">
        <span class="material-symbols-outlined text-[22px] transition-transform group-hover:scale-110">
            notifications
        </span>
        
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-primary rounded-full border-2 border-background-dark ring-2 ring-primary/20 animate-pulse"></span>
        @endif
    </a>
</div>
