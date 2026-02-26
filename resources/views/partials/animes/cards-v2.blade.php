@foreach ($animes as $anime)
    @php
        $thumbnailUrl = $anime->thumbnail_src;

        if ($anime->thumbnail && Storage::disk('public')->exists($anime->thumbnail)) {
            $thumbnailUrl = Storage::url($anime->thumbnail);
        }
    @endphp
    {{-- <article class="tarjeta">
        <div class="textos">
            <div class="tarjeta-header ">
                <h3 class="text-shadow text-uppercase post-titles">{{ $song->anime->title }}</h3>
            </div>
            @if ($song->theme_num > 1)
                <div class="{{ $song->type == 'OP' ? 'tag' : 'tag2' }}">
                    <span class="tag-content ">{{ $song->theme_num > 1 ? $song->slug : $song->type }}</span>
                </div>
            @endif

            <a class="no-deco" href="{{ $song->urlFirstVariant }}" rel="nofollow noopener noreferrer">
                <img class="thumb" loading="lazy" src="{{ $thumbnailUrl }}" alt="{{ $song->anime->title }}"
                    title="{{ $song->anime->title }}">
            </a>
            <div class="tarjeta-footer ">
                <span>{{ $song->likeCount }} <i class="fa fa-heart"></i></span>
                <span>{{ $song->view_count }} <i class="fa fa-eye"></i></span>
                @if (isset($song->rating))
                    <span style="color: rgb(162, 240, 181)">{{ $song->rating != null ? $song->rating : '0' }} <i
                            class="fa fa-star" aria-hidden="true"></i>
                    </span>
                @else
                    <span>{{ $song->score != null ? $song->score : 'n/a' }} <i class="fa fa-star"
                            aria-hidden="true"></i>
                    </span>
                @endif
            </div>
        </div>
        <div>
            <span>{{ $song->anime->title }}</span>
        </div>
    </article> --}}

    <div class="media-card">
        <div class="position-relative overflow-hidden">
            <a href="{{ $anime->url }}" class="cover">
                <img class="image loaded z-0" loading="lazy" src="{{ $thumbnailUrl }}" alt="{{ $anime->title }}">
            </a>
        </div>
        <div>
            <a href="{{ $anime->url }}" class="title">
                {{ $anime->title }}
            </a>
        </div>
    </div>
@endforeach
