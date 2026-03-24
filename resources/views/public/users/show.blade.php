@extends('layouts.app')

@section('meta')
    <title>{{ $user->name }} - List | {{ config('app.name') }}</title>
    <meta title="{{ $user->name }} - Favorites Archive">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="description"
        content="Explore the personal collection of openings & endings from {{ $user->name }} on Anirank.">
    <meta name="robots" content="index, follow, max-image-preview:standard">
@endsection

@section('content')
    {{-- Modern User Banner --}}
    @include('partials.user.banner')

    <div class="max-w-[1440px] mx-auto px-4 md:px-8">
        @if($user->about)
            <div class="mt-12 md:mt-16">
                <div class="bg-surface/30 p-8 md:p-10 rounded-2xl border border-border-site shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-6 opacity-40">
                        <span class="material-symbols-outlined text-sm">description</span>
                        <span class="text-[10px] uppercase font-black tracking-widest">About</span>
                    </div>
                    <div class="text-text/90 leading-relaxed markdown-content prose-invert">
                        {!! Str::markdown($user->about) !!}
                    </div>
                </div>
            </div>
        @endif

        <div class="py-12 md:py-16">
            {{-- Livewire Favorites Table --}}
            @livewire('user-favorites-table', ['userId' => $user->id])
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Legacy AJAX filter scripts removed --}}
@endpush
