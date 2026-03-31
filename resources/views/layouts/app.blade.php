<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="block-all-mixed-content">

    @include('partials.meta')

    {{-- Favicons --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('resources/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('resources/images/favicon-16x16.png') }}">
    <link rel="shortcut icon" sizes="512x512" href="{{ asset('resources/images/logo3.svg') }}">

    {{-- PWA & Mobile --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0E3D5F">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('resources/images/apple-touch-icon.png') }}">
    <link rel="mask-icon" href="{{ asset('resources/images/safari-pinned-tab.svg') }}" color="#0E3D5F">
    <meta name="msapplication-TileImage" content="{{ asset('resources/images/msapplication-icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#0E3D5F">

    {{-- Theme Detection (Blocking to prevent flickers) --}}
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Main Assets (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/ajaxSearch.js'])

    @auth

    @endauth

    @livewireStyles
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-background text-text font-display min-h-screen flex flex-col overflow-x-hidden antialiased selection:bg-primary selection:text-white transition-colors duration-300">
    <div id="app">
        @include('layouts.navbar')

        <main>
            @include('layouts.alerts')

            @yield('content')
            {{ $slot ?? '' }}

            @include('layouts.modal-search')

            @auth
                <livewire:request-modal />
                <livewire:report-modal />
                <livewire:user-report-modal />
            @endauth
        </main>

        @include('layouts.footer')

        <x-toast />
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>
