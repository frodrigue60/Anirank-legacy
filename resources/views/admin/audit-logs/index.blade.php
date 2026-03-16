@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-zinc-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>
                Audit Logs
            </h1>

            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search logs..."
                        class="bg-zinc-900 border-zinc-800 text-zinc-100 rounded-lg pl-10 pr-4 py-2 focus:ring-primary focus:border-primary w-64">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-zinc-500 text-sm">search</span>
                </div>

                <select name="event" onchange="this.form.submit()"
                    class="bg-zinc-900 border-zinc-800 text-zinc-100 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                    <option value="">All Events</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>

                <select name="type" onchange="this.form.submit()"
                    class="bg-zinc-900 border-zinc-800 text-zinc-100 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                    <option value="">All Types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['q', 'event', 'type']))
                    <a href="{{ route('admin.audit-logs.index') }}" 
                       class="text-zinc-400 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl overflow-hidden glass-panel">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 bg-zinc-900/80">
                            <th class="p-4 font-semibold text-zinc-400">User</th>
                            <th class="p-4 font-semibold text-zinc-400">Event</th>
                            <th class="p-4 font-semibold text-zinc-400">Resource</th>
                            <th class="p-4 font-semibold text-zinc-400">Details</th>
                            <th class="p-4 font-semibold text-zinc-400">IP / Meta</th>
                            <th class="p-4 font-semibold text-zinc-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @if($log->user)
                                            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-bold shrink-0">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-medium truncate text-zinc-100">{{ $log->user->name }}</div>
                                                <div class="text-xs text-zinc-500 truncate">{{ $log->user->email }}</div>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-500 text-xs shrink-0">
                                                <span class="material-symbols-outlined text-sm">precision_manufacturing</span>
                                            </div>
                                            <span class="text-zinc-500 italic">System</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span @class([
                                        'px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider',
                                        'bg-green-500/10 text-green-400' => $log->event === 'created',
                                        'bg-blue-500/10 text-blue-400' => $log->event === 'updated',
                                        'bg-red-500/10 text-red-400' => $log->event === 'deleted',
                                    ])>
                                        {{ $log->event }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm">
                                    <div class="text-zinc-300 font-medium">{{ ucfirst($log->auditable_type) }}</div>
                                    <div class="text-zinc-500 text-xs capitalize">ID: {{ $log->auditable_id }}</div>
                                </td>
                                <td class="p-4 max-w-xs">
                                    @if($log->event === 'updated' && $log->old_values)
                                        <div class="space-y-1">
                                            @foreach($log->new_values as $key => $val)
                                                <div class="text-xs">
                                                    <span class="text-zinc-500">{{ $key }}:</span>
                                                    <span class="text-red-400/80 line-through decoration-red-500/50 mr-1">{{ $log->old_values[$key] ?? 'null' }}</span>
                                                    <span class="material-symbols-outlined text-[10px] text-zinc-600 align-middle">arrow_forward</span>
                                                    <span class="text-green-400">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($log->event === 'created' && $log->new_values)
                                        <div class="text-xs text-zinc-400 italic">
                                            Initial data created.
                                        </div>
                                    @else
                                        <div class="text-xs text-zinc-600 italic">No details logged</div>
                                    @endif
                                </td>
                                <td class="p-4 text-xs text-zinc-500">
                                    <div>{{ $log->ip_address }}</div>
                                    <div class="truncate max-w-[120px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                                </td>
                                <td class="p-4 text-sm text-zinc-400">
                                    {{ $log->created_at->diffForHumans() }}
                                    <div class="text-[10px] text-zinc-600">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-500 italic">
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-zinc-800 bg-zinc-900/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
