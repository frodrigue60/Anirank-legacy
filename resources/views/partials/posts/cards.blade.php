@foreach ($animes as $anime)
    @php
        $thumbnail_url = $anime->cover_url_src;

        if ($anime->cover_url && Storage::disk('public')->exists($anime->cover_url)) {
            $thumbnail_url = Storage::url($anime->cover_url);
        }
    @endphp
    <article class="tarjeta">
        <a class="no-deco" href="{{ $anime->url }}" rel="nofollow noopener noreferrer">
            <div class="textos">
                <div class="tarjeta-header ">
                    <h3 class="text-shadow text-uppercase post-titles">{{ $anime->title }}</h3>
                </div>
                <img class="thumb" loading="lazy" src="{{ $thumbnail_url }}" alt="{{ $anime->title }}"
                    title="{{ $anime->title }}">
                <div class="tarjeta-footer justify-content-center">
                    <span class="">
                        {{ $anime->songs->count() }} <i class="fa-solid fa-music"></i>
                    </span>
                </div>
            </div>
        </a>
    </article>
@endforeach


