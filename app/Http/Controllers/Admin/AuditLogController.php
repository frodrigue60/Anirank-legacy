<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\Breadcrumb;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = Breadcrumb::generate([
            ['name' => 'Audit Logs', 'url' => ''],
        ]);

        $query = AuditLog::with('user')->latest();

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('auditable_type', 'like', "%{$request->q}%")
                  ->orWhere('event', 'like', "%{$request->q}%")
                  ->orWhere('ip_address', 'like', "%{$request->q}%")
                  ->orWhereHas('user', function($userQ) use ($request) {
                      $userQ->where('name', 'like', "%{$request->q}%")
                            ->orWhere('email', 'like', "%{$request->q}%");
                  });
            });
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('type')) {
            $query->where('auditable_type', $request->type);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $events = AuditLog::distinct()->pluck('event');
        $types = AuditLog::distinct()->pluck('auditable_type');

        return view('admin.audit-logs.index', compact('logs', 'breadcrumb', 'events', 'types'));
    }
}
