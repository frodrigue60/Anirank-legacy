@extends('layouts.app')
@section('meta')
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection
@section('content')
    <livewire:ranking-table />
@endsection

@push('scripts')
    {{-- Livewire handles data fetching and infinite scroll --}}
@endpush
