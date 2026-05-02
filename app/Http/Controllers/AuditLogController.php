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
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
