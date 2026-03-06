<div>
    <div class="flex items-center gap-3">
        <button wire:click="toggle"
            class="flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-300 font-bold text-sm shadow-lg
            {{ $isFavorite
                ? 'bg-red-500 text-white shadow-red-500/20 hover:bg-red-600'
                : 'bg-white/5 text-white/70 hover:bg-white/10 border border-white/5' }}">

            <span
                class="material-symbols-outlined filled text-[20px] {{ $isFavorite ? 'animate-bounce-short text-red-400' : '' }}">
                favorite
            </span>

            <span>{{ $isFavorite ? 'In Favorites' : 'Add to Favorites' }}</span>

            @if ($favoriteCount > 0)
                <span class="ml-1 pl-2 border-l border-current/20 opacity-80">{{ number_format($favoriteCount) }}</span>
            @endif
        </button>
    </div>

    <style>
        @keyframes bounce-short {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .animate-bounce-short {
            animation: bounce-short 0.3s ease-out;
        }
    </style>
</div>
