@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small mb-1">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['project_submitted','project_approved','project_rejected'] as $a)
                    <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>
                        {{ str_replace('_',' ',ucfirst($a)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small mb-1">Project ID</label>
                <input type="number" name="project_id" class="form-control form-control-sm"
                       value="{{ request('project_id') }}" placeholder="ID">
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Project</th>
                        <th>Performed By</th>
                        <th>Meta</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted">{{ $log->id }}</td>
                        <td>
                            <span class="badge
                                @if(str_contains($log->action,'approved')) bg-success
                                @elseif(str_contains($log->action,'rejected')) bg-danger
                                @else bg-secondary @endif">
                                {{ str_replace('_',' ',$log->action) }}
                            </span>
                        </td>
                        <td>
                            @if($log->project)
                            <a href="{{ route('projects.show', $log->project) }}" class="text-decoration-none">
                                #{{ $log->project_id }} {{ Str::limit($log->project->title, 25) }}
                            </a>
                            @else
                            <span class="text-muted">#{{ $log->project_id }} (deleted)</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $log->user->name ?? '—' }}</td>
                        <td class="text-muted small">
                            {{ $log->meta ? json_encode($log->meta) : '—' }}
                        </td>
                        <td class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
