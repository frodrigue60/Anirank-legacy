@php
    $defaultTitle = 'Ranking Anime Openings & Endings | ' . config('app.name');
    $defaultDescription =
        'The site you were looking for to rate openings and endings of your favorite animes. Discover which are the most popular opening and endings.';
    $defaultKeywords =
        'top anime openings, top anime endings, ranking openings anime, ranking endings anime, Best Anime Openings Of All Time, openings anime, endings anime';
    $defaultOgImage = asset('resources/images/og-image-wide.png');
    $currentUrl = url()->current();
@endphp

{{-- Standard SEO --}}
<title>@yield('title', $defaultTitle)</title>
<meta name="title" content="@yield('title', $defaultTitle)">
<meta name="description" content="@yield('description', $defaultDescription)">
<meta name="keywords" content="@yield('keywords', $defaultKeywords)">
<link rel="canonical" href="{{ $currentUrl }}">
<meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:standard')">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:title" content="@yield('title', $defaultTitle)">
<meta property="og:description" content="@yield('description', $defaultDescription)">
<meta property="og:image" content="@yield('og_image', $defaultOgImage)">
<meta property="og:image:secure_url" content="@yield('og_image', $defaultOgImage)">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="828">
<meta property="og:image:height" content="450">
<meta property="og:image:alt" content="@yield('title', $defaultTitle)" />

{{-- Twitter --}}
<meta name="twitter:card" content="@yield('twitter_card', 'summary')">
<meta name="twitter:site" content="@frodrigue60">
<meta name="twitter:creator" content="@frodrigue60">
<meta name="twitter:title" content="@yield('title', $defaultTitle)">
<meta name="twitter:description" content="@yield('description', $defaultDescription)">
<meta name="twitter:image" content="@yield('og_image', $defaultOgImage)">

{{-- Preload & Preconnect for Performance --}}
{{-- <link rel="preconnect" href="https://fonts.googleapis.com"> --}}
{{-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> --}}
