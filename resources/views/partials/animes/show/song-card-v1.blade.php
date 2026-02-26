<div class=" p-2 d-flex flex-column gap-1 my-2 rounded-2">
    <div class="d-flex justify-content-between">
        <h4 class="p-0 m-0">{{ $song->slug }}</h4>
    </div>
    <div class="d-flex flex-column">
        <span class=" text-decoration-none"><span class="pe-1"><i class="fa-solid fa-music"></i></span>
            {{ $song->name }}</span>
        @isset($song->artists)
            <div>
                <span class="pe-2"><i class="fa-solid fa-user"></i></span>
                @foreach ($song->artists as $index => $item)
                    <a class=" text-decoration-none" href="{{ $item->url }}">{{ $item->name }}</a>
                    @if ($index < count($song->artists) - 1)
                        ,
                    @endif
                @endforeach
            </div>
        @endisset
    </div>
    <hr class="p-0 m-0">
    @isset($song->songVariants)
        @if ($song->songVariants->count() != 0)
            <div class="d-flex flex-column gap-2 mx-2 py-2">
                @foreach ($song->songVariants->sortBy('version') as $variant)
                    <div class="d-flex justify-content-between align-items-center">
                        <a class="text-decoration-none " href="{{ $variant->url }}"><span>Version
                                {{ $variant->version_number }}</span></a>
                        <div class="d-flex flex-row align-items-center gap-4">
                            <div>
                                @if (isset($variant->score))
                                    <span>{{ $variant->scoreString }} <i class="fa-solid fa-star"></i></span>
                                @else
                                    <span>N/A <i class="fa-solid fa-star"></i></span>
                                @endif
                            </div>
                            <div>
                                <a class="btn btn-sm btn-primary rounded-4" href="{{ $variant->url }}">{{ 'Show' }}
                                    <i class="fa-solid fa-play"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h4 class="text-center">No videos</h4>
        @endif
    @endisset
</div>
