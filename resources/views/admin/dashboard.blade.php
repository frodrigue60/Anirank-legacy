@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Admin Dashboard</h1>
                <p class="text-zinc-400 mt-1 uppercase text-[10px] font-black tracking-widest text-primary">
                    Platform Analytics & Performance
                </p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.posts.track.ranking') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 border border-primary/20 rounded-2xl text-primary transition-all active:scale-95 group">
                        <span
                            class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">trending_up</span>
                        <span class="text-[10px] font-black uppercase tracking-widest">Recalculate Ranking</span>
                    </button>
                </form>

                <div class="flex items-center gap-2 px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-2xl">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">System Live</span>
                </div>
            </div>
        </div>

        @if (Auth::check() && Auth::user()->isStaff())
            {{-- Quick Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-admin.stat-card icon="visibility" color="blue" label="Total Views"
                    value="{{ number_format($stats['total_views']) }}" />
                <x-admin.stat-card icon="person" color="emerald" label="Total Users"
                    value="{{ number_format($stats['total_users']) }}" />
                <x-admin.stat-card icon="login" color="amber" label="Active Users (24h)"
                    value="{{ number_format($stats['active_users_24h']) }}" />
                <x-admin.stat-card icon="music_note" color="purple" label="Total Content"
                    value="{{ number_format($stats['total_songs']) }}" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Views Chart --}}
                <div
                    class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-3xl p-6 overflow-hidden relative group">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight uppercase">Views Traffic</h3>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Last 7 days performance
                            </p>
                        </div>
                        <div class="p-2 bg-zinc-800 rounded-xl">
                            <span class="material-symbols-outlined text-zinc-400">show_chart</span>
                        </div>
                    </div>

                    <div class="h-[300px]">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>

                {{-- Trending Songs --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight uppercase">Trending Now</h3>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Top 5 by weekly views
                            </p>
                        </div>
                        <div class="p-2 bg-zinc-800 rounded-xl">
                            <span class="material-symbols-outlined text-amber-500">local_fire_department</span>
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        @forelse ($trendingSongs as $song)
                            <div
                                class="bg-zinc-950/50 border border-zinc-800 rounded-2xl p-4 flex items-center gap-4 group hover:border-zinc-700 transition-all">
                                <div class="w-12 h-16 rounded-lg bg-zinc-900 overflow-hidden shrink-0">
                                    <img src="{{ $song->post->thumbnail_url }}"
                                        class="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all"
                                        alt="">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-white truncate">{{ $song->name }}</h4>
                                    <p class="text-[10px] text-zinc-500 font-black tracking-widest uppercase truncate">
                                        {{ $song->post->title }}</p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-xs font-black text-zinc-300">{{ number_format($song->recent_views) }}</span>
                                    <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-widest">Views</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">No data available
                                    yet</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('admin.songs.index') }}"
                        class="mt-6 w-full py-3 bg-zinc-800 hover:bg-zinc-700 text-[10px] font-black text-zinc-400 hover:text-white text-center rounded-2xl tracking-widest uppercase transition-all">
                        View Detailed Metrics
                    </a>
                </div>
            </div>
        @else
            {{-- Restrict Access UI --}}
            <div class="bg-rose-500/10 border border-rose-500/20 rounded-3xl p-12 text-center max-w-2xl mx-auto">
                <div
                    class="w-20 h-20 bg-rose-500/20 rounded-3xl flex items-center justify-center text-rose-500 mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">lock</span>
                </div>
                <h3 class="text-2xl font-black text-rose-400 mb-3">Access Restricted</h3>
                <p class="text-white/60 mb-8">You do not have the required staff privileges to access this area.</p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center px-6 py-3 bg-white text-black font-bold rounded-2xl hover:bg-zinc-200 transition-all active:scale-95">
                    RETURN TO HOME
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('viewsChart').getContext('2d');

            // Premium Chart Styling
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData->keys()) !!},
                    datasets: [{
                        label: 'Page Views',
                        data: {!! json_encode($chartData->values()) !!},
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#09090b',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#71717a',
                                font: {
                                    weight: 'bold',
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#71717a',
                                font: {
                                    weight: 'bold',
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
