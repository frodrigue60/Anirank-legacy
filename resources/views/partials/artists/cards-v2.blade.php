@foreach ($artists as $artist)
    @php
        $thumbnailUrl = '';

        if ($artist->cover_url != null && Storage::disk('public')->exists($artist->cover_url)) {
            $thumbnailUrl = Storage::url($artist->cover_url);
        } elseif ($artist->cover_url_src != null) {
            $thumbnailUrl = $artist->cover_url_src;
        } else {
            $thumbnailUrl =  asset('resources/images/default-thumbnail.jpg') ;
        }
    @endphp
    <div class="media-card">
        <div class="position-relative overflow-hidden">
            <a href="{{ $artist->url }}" class="cover">
                <img class="image loaded z-0" loading="lazy" src="{{ $thumbnailUrl }}" alt="{{ $artist->title }}">
            </a>
        </div>
        <div>
            <a href="{{ $artist->url }}" class="title">
                {{ $artist->name }}
            </a>
        </div>
    </div>
@endforeach


