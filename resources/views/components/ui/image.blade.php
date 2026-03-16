@props(['src' => null, 'fallback' => null, 'alt' => '', 'lazy' => true])

@php
    $finalSrc = $src;
    if ($src && !filter_var($src, FILTER_VALIDATE_URL)) {
        $finalSrc = \Illuminate\Support\Facades\Storage::url($src);
    }

    $finalFallback = $fallback;
    if ($fallback && !filter_var($fallback, FILTER_VALIDATE_URL)) {
        $finalFallback = \Illuminate\Support\Facades\Storage::url($fallback);
    }
@endphp

@if ($finalSrc)
    <img src="{{ $finalSrc }}" alt="{{ $alt }}" @if ($lazy) loading="lazy" @endif
        @if ($finalFallback) onerror="this.onerror=null;this.src='{{ $finalFallback }}';" @endif
        {{ $attributes->merge(['class' => 'object-cover']) }}>
@elseif ($finalFallback)
    <img src="{{ $finalFallback }}" alt="{{ $alt }}" @if ($lazy) loading="lazy" @endif
        {{ $attributes->merge(['class' => 'object-cover']) }}>
@endif
