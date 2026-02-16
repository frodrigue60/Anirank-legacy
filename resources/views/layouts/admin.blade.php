<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | {{ config('app.name', 'Anirank') }}</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts & Icons --}}
    @include('layouts.fonts-links')

    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-zinc-950 text-zinc-100 font-sans antialiased selection:bg-primary selection:text-white"
    x-data="{
        sidebarOpen: localStorage.getItem('sidebarOpen') === 'false' ? false : true,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    }">

    <div class="flex min-h-screen bg-zinc-950">
        {{-- Sidebar --}}
        <x-admin.sidebar />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="sidebarOpen ? 'pl-64' : 'pl-20'">
            {{-- Header --}}
            <x-admin.header />

            {{-- Page Content --}}
            <main class="flex-1 p-4 md:p-8 overflow-y-auto">
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <x-toast />
</body>

</html>
