@foreach ($animes as $anime)
    @php
        $thumbnailUrl = $anime->cover_url_src;

        if ($anime->cover_url && Storage::disk('public')->exists($anime->cover_url)) {
            $thumbnailUrl = Storage::url($anime->cover_url);
        }
    @endphp
    {{-- <article class="tarjeta">
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
    </article> --}}
    <div class="d-flex flex-column p-2 color1 rounded-1" {{-- style="border: solid 1px red;" --}}>
        <a class="d-flex gap-3 no-deco " data-bs-toggle="collapse" href="#collapseExample{{ $anime->id }}" role="button"
            aria-expanded="false" aria-controls="collapseExample{{ $anime->id }}">
            <div class="d-flex">
                <img class="rounded-1" src="{{ $thumbnailUrl }}" alt="" style="max-width: 80px;height:auto;">
            </div>
            <div>
                <div>
                    <p class="d-inline-block text-truncate">{{ $anime->title }}</p>
                    <p> {{ $anime->season->name }} {{ $anime->year->name }}</p>
                </div>
            </div>
        </a>
        <div class="collapse mt-2" id="collapseExample{{ $anime->id }}">
            <div class="">
                <table class="table table-sm  mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anime->songs as $song)
                            @foreach ($song->songVariants as $variant)
                                <tr>
                                    <td>{{ $variant->song->slug }} {{ $variant->slug }}</td>
                                    <td>
                                        <a href="{{ $variant->url }}">
                                            {{ $variant->song->name }} {{ $variant->slug }}
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach


