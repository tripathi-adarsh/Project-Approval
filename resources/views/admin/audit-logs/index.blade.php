@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold text-dark">Audit Logs</h5>
        <small class="text-muted">Full history of all project actions</small>
    </div>
    <span class="badge bg-primary rounded-pill fs-6 px-3">{{ $logs->total() }} Records</span>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1 text-muted">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach(['project_submitted','project_approved','project_rejected'] as $a)
                    <option value="{{ $a }}" {{ request('action')===$a ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $a)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1 text-muted">Project ID</label>
                <input type="number" name="project_id" class="form-control form-control-sm"
                       value="{{ request('project_id') }}" placeholder="e.g. 42" min="1">
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1 text-muted">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1 text-muted">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-sm-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                @if(request()->hasAny(['action','project_id','from','to']))
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Active filter badges --}}
@if(request()->hasAny(['action','project_id','from','to']))
<div class="d-flex flex-wrap gap-2 mb-3">
    @if(request('action'))
    <span class="badge bg-light text-dark border">
        <i class="bi bi-activity me-1"></i>{{ ucwords(str_replace('_', ' ', request('action'))) }}
    </span>
    @endif
    @if(request('project_id'))
    <span class="badge bg-light text-dark border"><i class="bi bi-hash me-1"></i>Project {{ request('project_id') }}</span>
    @endif
    @if(request('from'))
    <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>From: {{ request('from') }}</span>
    @endif
    @if(request('to'))
    <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>To: {{ request('to') }}</span>
    @endif
</div>
@endif

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Action</th>
                        <th>Project</th>
                        <th>Performed By</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted small">{{ $log->id }}</td>
                        <td>
                            @php
                                $actionColor = str_contains($log->action, 'approved') ? 'success'
                                    : (str_contains($log->action, 'rejected') ? 'danger' : 'secondary');
                                $actionIcon  = str_contains($log->action, 'approved') ? 'bi-check-circle'
                                    : (str_contains($log->action, 'rejected') ? 'bi-x-circle' : 'bi-send');
                            @endphp
                            <span class="badge rounded-pill bg-{{ $actionColor }}">
                                <i class="bi {{ $actionIcon }} me-1"></i>
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            @if($log->project)
                            <a href="{{ route('projects.show', $log->project) }}"
                               class="text-decoration-none fw-semibold text-dark">
                                #{{ $log->project_id }}
                            </a>
                            <span class="text-muted small ms-1">{{ Str::limit($log->project->title, 28) }}</span>
                            @else
                            <span class="text-muted small">#{{ $log->project_id }} <em>(deleted)</em></span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                     style="width:28px;height:28px;font-size:.7rem;flex-shrink:0">
                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-muted small">{{ $log->user->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-muted small">
                            @if(!empty($log->meta['reason']))
                                <span class="text-truncate d-inline-block" style="max-width:180px"
                                      title="{{ $log->meta['reason'] }}">
                                    <i class="bi bi-chat-left-text me-1"></i>{{ Str::limit($log->meta['reason'], 40) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <span title="{{ $log->created_at->format('d M Y, H:i:s') }}">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </span>
                            <div class="text-muted" style="font-size:.7rem">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-50"></i>
                                @if(request()->hasAny(['action','project_id','from','to']))
                                    <p class="mb-2 fw-semibold">No logs match your filters.</p>
                                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                @else
                                    <p class="mb-0 fw-semibold">No audit logs found.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} records
        </small>
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
