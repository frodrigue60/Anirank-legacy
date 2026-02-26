@extends('layouts.app')

@section('meta')
    <title>Producers | {{ config('app.name') }}</title>
    <meta title="Producers">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="description" content="Explore production companies and committees.">
    <meta name="robots" content="index, follow, max-image-preview:standard">
@endsection

@section('content')
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-10 md:py-8 flex flex-col gap-12">
        {{-- Livewire Table component --}}
        @livewire('producers-table')
    </div>
@endsection
