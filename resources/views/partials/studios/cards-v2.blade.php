@foreach ($studios as $studio)
    @php
        $thumbnailUrl = '';

        if ($studio->cover_url != null && Storage::disk('public')->exists($studio->cover_url)) {
            $thumbnailUrl = Storage::url($studio->cover_url);
        } elseif ($studio->cover_url_src != null) {
            $thumbnailUrl = $studio->cover_url_src;
        } else {
            $thumbnailUrl = asset('resources/images/default-thumbnail.jpg');
        }
    @endphp
    <div class="media-card">
        <div class="position-relative overflow-hidden">
            <a href="{{ route('studios.show', $studio) }}" class="cover">
                <img class="image loaded z-0" loading="lazy" src="{{ $thumbnailUrl }}" alt="{{ $studio->name }}">
            </a>
        </div>
        <div>
            <a href="{{ route('studios.show', $studio) }}" class="title">
                {{ $studio->name }}
            </a>
        </div>
    </div>
@endforeach


