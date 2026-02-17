@foreach ($studios as $studio)
    @php
        $thumbnailUrl = '';

        if ($studio->thumbnail != null && Storage::disk('public')->exists($studio->thumbnail)) {
            $thumbnailUrl = Storage::url($studio->thumbnail);
        } elseif ($studio->thumbnail_src != null) {
            $thumbnailUrl = $studio->thumbnail_src;
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
