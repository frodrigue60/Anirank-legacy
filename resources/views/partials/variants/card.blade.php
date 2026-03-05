@php
    $version = $variant->version_number;
    $forward_text =
        ($variant->song->slug ? $variant->song->slug : $variant->song->type) . 'v' . $variant->version_number;

    $anime = $variant->song->post;
    $title = $anime->title;

    if (Storage::disk('public')->exists($anime->cover_url)) {
        $thumbnail_url = Storage::url($anime->cover_url);
    } else {
        $thumbnail_url = $anime->cover_url_src;
    }

    $likeCount = 0;
    if ($variant->likesCount >= 1000000) {
        $likeCount = number_format(intval($variant->likesCount / 1000000), 0) . 'M';
    } elseif ($variant->likesCount >= 1000) {
        $likeCount = number_format(intval($variant->likesCount / 1000), 0) . 'K';
    } else {
        $likeCount = $variant->likesCount;
    }
@endphp

<article class="tarjeta">
    <a class="no-deco" href="{{ $variant->url }}" rel="nofollow noopener noreferrer">
        <div class="textos">
            <div class="tarjeta-header ">
                <h3 class="text-shadow text-uppercase post-titles">{{ $title }}</h3>
            </div>
            <div class="{{ $variant->song->type == '1' ? 'tag' : 'tag2' }}">
                <span class="tag-content ">{{ $forward_text }}</span>
            </div>
            <img class="thumb" loading="lazy" src="{{ $thumbnail_url }}" alt="{{ $title }}"
                title="{{ $title }}">
            <div class="tarjeta-footer ">
                <span>{{ $likeCount }} <i class="fa-solid fa-heart"></i></span>
                <span>{{ $variant->viewsString }} <i class="fa-solid fa-eye"></i></span>
                <span>{{ $variant->score }}
                    @if (isset($variant->userScore))
                        <i style="color: rgb(162, 240, 181);" class="fa-solid fa-star" aria-hidden="true"></i>
                    @else
                        <i class="fa-solid fa-star" aria-hidden="true"></i>
                    @endif
                </span>
            </div>
        </div>
    </a>
</article>


