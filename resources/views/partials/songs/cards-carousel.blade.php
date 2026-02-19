@foreach ($songs as $song)
    <div class="media-card">
        <a href="{{ $song->url }}" class="cover">
            <div class="absolute top-0 z-20">
                <div class="flex gap-1 p-1">
                    {{-- <span
                        class="bg-black/50 rounded-sm text-sm px-1 shadow-lg">{{ number_format($song->average_rating ?? 0, 1) }}</span> --}}
                    <span class="bg-black/50 rounded-sm text-sm px-1 shadow-lg">{{ $song->slug }}</span>
                </div>
            </div>
            <img class="image loaded" loading="lazy" src="{{ $song->post->thumbnail_url }}" alt="{{ $song->post->title }}">
        </a>
        <a href="{{ $song->url }}" class="title">
            {{ $song->post->title }}
        </a>
    </div>
@endforeach
