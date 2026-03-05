@props(['src' => null, 'fallback' => null, 'alt' => '', 'lazy' => true])

@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}" @if ($lazy) loading="lazy" @endif
        {{ $attributes->merge(['class' => 'object-cover']) }}>
@endif
