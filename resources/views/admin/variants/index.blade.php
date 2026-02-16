@extends('layouts.admin')

@section('title', 'Manage Variants')

@section('content')
    <div class="space-y-8">
        {{-- Custom Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Song Variants</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest">Audio Version Control</p>
            </div>
        </div>
        {{-- The rest of the content for the admin variants index page would go here --}}
        {{-- For example, a table similar to the original, but within the new layout structure --}}
        <div class="table-responsive">
            <table class="table ">
                <thead>
                    <tr>
                        <th scope="col">Column 1</th>
                        <th scope="col">Column 2</th>
                        <th scope="col">Column 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td scope="row">R1C1</td>
                        <td>R1C2</td>
                        <td>R1C3</td>
                    </tr>
                    <tr class="">
                        <td scope="row">Item</td>
                        <td>Item</td>
                        <td>Item</td>
                    </tr>
                </tbody>
            </table>
        </div>

    @endsection
