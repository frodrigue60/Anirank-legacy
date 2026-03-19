@extends('layouts.app')

@section('title', 'Global Statistics')
@section('description', 'Explore the growth and activity of the Anirank community through interactive charts and real-time data.')

@section('content')
    <div class="container mx-auto px-4 py-12">
        {{-- Header --}}
        <div class="mb-12 text-center md:text-left">
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4 drop-shadow-xl">
                Global <span class="text-primary italic">Stats</span>
            </h1>
            <p class="text-white/40 text-lg max-w-2xl font-medium leading-relaxed">
                Insights into the Anirank ecosystem, community habits, and database growth.
            </p>
        </div>

        {{-- Totals Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-12">
            @foreach([
                ['label' => 'Total Users', 'value' => $totals['users'], 'icon' => 'group'],
                ['label' => 'Theme Songs', 'value' => $totals['songs'], 'icon' => 'music_note'],
                ['label' => 'Anime Series', 'value' => $totals['animes'], 'icon' => 'movie'],
                ['label' => 'Total Ratings', 'value' => $totals['ratings'], 'icon' => 'star'],
                ['label' => 'Artists', 'value' => $totals['artists'], 'icon' => 'artist'],
            ] as $stat)
                <div class="glass-panel p-6 rounded-3xl border border-white/5 flex flex-col items-center justify-center text-center group hover:border-primary/30 transition-all duration-500 overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all"></div>
                    <span class="material-symbols-outlined text-primary text-3xl mb-3 filled group-hover:scale-110 transition-transform duration-500">
                        {{ $stat['icon'] }}
                    </span>
                    <span class="text-2xl md:text-3xl font-black text-white mb-1">{{ number_format($stat['value']) }}</span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            {{-- Community Growth --}}
            <div class="glass-panel p-6 md:p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden h-full">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight">Community Growth</h2>
                        <p class="text-white/30 text-xs font-bold uppercase tracking-widest mt-1">Users & Ratings (Last 12 Months)</p>
                    </div>
                </div>
                <div id="growthChart" class="w-full h-80"></div>
            </div>

            {{-- Voting Habits --}}
            <div class="glass-panel p-6 md:p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden h-full">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight">Voting Habits</h2>
                        <p class="text-white/30 text-xs font-bold uppercase tracking-widest mt-1">Star Distribution Across All Songs</p>
                    </div>
                </div>
                <div id="votingHabitsChart" class="w-full h-80 flex items-center justify-center"></div>
            </div>

            {{-- Level Hierarchy --}}
            <div class="glass-panel p-6 md:p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden lg:col-span-2">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight">Level Hierarchy</h2>
                        <p class="text-white/30 text-xs font-bold uppercase tracking-widest mt-1">User Distribution by Level Progression</p>
                    </div>
                </div>
                <div id="levelChart" class="w-full h-64"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Styles & Colors
            const colors = {
                primary: '#7f13ec',
                secondary: '#a855f7',
                accent: '#c084fc',
                bg: '#191022',
                surface: '#2a2136',
                grid: 'rgba(255, 255, 255, 0.05)',
                text: 'rgba(255, 255, 255, 0.4)'
            };

            const baseOptions = {
                theme: { mode: 'dark' },
                chart: {
                    background: 'transparent',
                    toolbar: { show: false },
                    sparkline: { enabled: false },
                    fontFamily: 'Spline Sans, sans-serif'
                },
                grid: {
                    borderColor: colors.grid,
                    strokeDashArray: 4,
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                stroke: { curve: 'smooth', width: 3 },
                markers: { size: 4, colors: [colors.primary], strokeColors: '#fff', strokeWidth: 2 }
            };

            // 1. Community Growth Chart
            const userGrowthData = @json($userGrowth);
            const ratingActivityData = @json($ratingActivity);

            const months = userGrowthData.map(d => d.month);
            const userCounts = userGrowthData.map(d => d.count);
            const ratingCounts = ratingActivityData.map(d => d.count);

            new ApexCharts(document.querySelector("#growthChart"), {
                ...baseOptions,
                series: [
                    { name: 'New Users', data: userCounts },
                    { name: 'Ratings', data: ratingCounts }
                ],
                colors: [colors.primary, colors.accent],
                xaxis: {
                    categories: months,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: colors.text, fontWeight: 700 } }
                },
                yaxis: {
                    labels: { style: { colors: colors.text, fontWeight: 700 } }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100]
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontWeight: 700,
                    offsetY: -30,
                    markers: { radius: 12 }
                }
            }).render();

            // 2. Voting Habits Chart (Donut)
            const votingData = @json($votingHabits);
            const scores = votingData.map(d => d.score + ' Stars');
            const voteCounts = votingData.map(d => d.count);

            new ApexCharts(document.querySelector("#votingHabitsChart"), {
                ...baseOptions,
                chart: { ...baseOptions.chart, type: 'donut', height: 320 },
                series: voteCounts,
                labels: scores,
                colors: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', colors.primary],
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: { show: true, fontWeight: 900, color: '#fff', offsetY: -10 },
                                value: { show: true, fontWeight: 900, color: '#fff', fontSize: '24px', offsetY: 10 },
                                total: {
                                    show: true,
                                    label: 'Total Votes',
                                    color: colors.text,
                                    fontWeight: 700,
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString()
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontWeight: 700,
                    markers: { radius: 12 }
                },
                stroke: { show: false }
            }).render();

            // 3. Level Hierarchy Chart
            const levelData = @json($levelDistribution);
            const levels = levelData.map(d => 'Lv.' + d.level);
            const levelUserCounts = levelData.map(d => d.count);

            new ApexCharts(document.querySelector("#levelChart"), {
                ...baseOptions,
                chart: { ...baseOptions.chart, type: 'bar' },
                series: [{ name: 'Users', data: levelUserCounts }],
                colors: [colors.primary],
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        distributed: true,
                        columnWidth: '60%',
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: levels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: colors.text, fontWeight: 700 } }
                },
                yaxis: {
                    labels: { style: { colors: colors.text, fontWeight: 700 } }
                },
                legend: { show: false }
            }).render();
        });
    </script>
    @endpush

    <style>
        .glass-panel {
            background: rgba(42, 33, 54, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
@endsection
