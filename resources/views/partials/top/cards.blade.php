{{-- @php
    $j = 1;
@endphp --}}
@foreach ($items as $song)
    @isset($song->post)
        @php
            $img_url = null;
            if ($song->anime->banner_url != null) {
                if (Storage::disk('public')->exists($song->anime->banner_url)) {
                    $img_url = Storage::url($song->anime->banner_url);
                }
            } else {
                $img_url =
                    'https://static.vecteezy.com/system/resources/thumbnails/005/170/408/small/banner-abstract-geometric-white-and-gray-color-background-illustration-free-vector.jpg';
            }

        @endphp
        <div class="card-2 mb-2">
            <img class="card-2-bg" src="{{ $img_url }}" alt="{{ $song->anime->title }}">
            <div class="gradient-1">
                {{-- <div class="m-2 fs-5">
                    <span><i class="fa-solid fa-award"></i></span>
                    <span class="">
                        # {{ $j++ }}
                    </span>
                </div> --}}
            </div>
            <div class="card-2-data p-2 d-flex flex-row justify-content-between w-100 gap-2">
                <div class="d-flex flex-column overflow-hidden">
                    @isset($song)
                        <a class="no-deco  bold text-truncate" href="{{ $song->urlFirstVariant }}">{{ $song->name }}
                        </a>
                        <span class="d-inline-block text-truncate">
                            @if (isset($song->artists) && count($song->artists) != 0)
                                @foreach ($song->artists as $index => $artist)
                                    <a class="no-deco " href="{{ $artist->url }}">{{ $artist->name }}</a>
                                    @if ($index < count($song->artists) - 1)
                                        ,
                                    @endif
                                @endforeach
                            @else
                                <span>N/A</span>
                            @endif
                        </span>
                        <a class="no-deco  text-truncate" href="{{ $song->anime->url }}">{{ $song->anime->title }}</a>
                    @endisset
                </div>
                <div class="d-flex align-items-end">
                    <div class="badge bg-dark rounded-pill  fw-medium">
                        <span id="score">{{ $song->scoreString }}</span>
                        <span>
                            @if ($song->userScore)
                                <i style="color: rgb(162, 240, 181)" class="fa fa-star" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-star" aria-hidden="true"></i>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endisset
@endforeach

