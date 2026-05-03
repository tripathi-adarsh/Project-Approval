<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with(['user', 'project'])
            ->when($request->input('project_id'), fn($q, $v) => $q->where('project_id', $v))
            ->when($request->input('action'),     fn($q, $v) => $q->where('action', $v))
            ->when($request->input('from'),       fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->input('to'),         fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
