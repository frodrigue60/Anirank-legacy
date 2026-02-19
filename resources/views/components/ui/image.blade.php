@props(['src' => null, 'fallback' => 'default-placeholder.webp', 'alt' => '', 'lazy' => true])

@php
    $finalSrc = $src ?: asset('img/placeholders/' . $fallback);
    $errorFallback = asset('img/placeholders/' . $fallback);
@endphp

<img src="{{ $finalSrc }}" alt="{{ $alt }}" @if ($lazy) loading="lazy" @endif
    onerror="this.onerror=null; this.src='{{ $errorFallback }}';" {{ $attributes->merge(['class' => 'object-cover']) }}>
