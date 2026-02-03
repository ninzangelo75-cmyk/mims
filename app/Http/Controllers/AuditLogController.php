<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Admin only
        abort_unless($request->user()->isAdmin(), 403);

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->through(function ($log) {
            return [
                'id' => $log->id,
                'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
                'user' => $log->user ? [
                    'useid' => $log->user->useid,
                    'fullname' => $log->user->fullname,
                ] : null,
                'action' => $log->action,
                'model_type' => class_basename($log->model_type),
                'model_id' => $log->model_id,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
            ];
        });

        return Inertia::render('AuditLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['user_id', 'action', 'model_type', 'date_from', 'date_to']),
        ]);
    }
}
