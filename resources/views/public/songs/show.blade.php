@extends('layouts.app')

@php
    $artists_string = $song->artists->pluck('name')->join(', ');
    $desc = 'Song: ' . $song->getNameAttribute() . ' - Artist: ' . $artists_string;
    $thumbnail_url = $post->thumbnail_url ?? asset('/storage/thumbnails/' . $post->thumbnail);
@endphp

@section('title', $post->title . ' ' . ($song->slug ?? $song->type))
@section('description', $desc)
@section('og_image', $thumbnail_url)
@section('og_type', 'article')

@section('meta')
    {{-- Plyr CSS --}}
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
@endsection

@section('content')
    <livewire:song-detail :song="$song" :post="$post" />
@endsection

@push('scripts')
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    {{-- Any other global scripts if needed --}}
@endpush
