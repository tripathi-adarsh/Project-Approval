@extends('layouts.app')
@section('title', 'My Projects')
@section('page-title', 'My Projects')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold text-dark">My Projects</h5>
        <small class="text-muted">Track all your submitted projects</small>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle-fill"></i> Submit New Project
    </a>
</div>

{{-- Filter Bar --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-5">
                <label class="form-label small fw-semibold mb-1 text-muted">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           value="{{ request('search') }}" placeholder="Search by title...">
                </div>
            </div>
            <div class="col-sm-3 col-md-3">
                <label class="form-label small fw-semibold mb-1 text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search','status']))
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="width:80px">File</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    <tr>
                        <td class="text-muted small">{{ $p->id }}</td>
                        <td>
                            <a href="{{ route('projects.show', $p) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ Str::limit($p->title, 50) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $p->statusBadgeClass() }}">
                                <i class="bi {{ $p->status === 'approved' ? 'bi-check-circle' : ($p->status === 'rejected' ? 'bi-x-circle' : 'bi-hourglass-split') }} me-1"></i>
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            @if($p->file_path)
                                <a href="{{ asset('storage/'.$p->file_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary" title="Download attachment">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $p) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-folder2-open fs-1 d-block mb-3 opacity-50"></i>
                                @if(request()->hasAny(['search','status']))
                                    <p class="mb-2 fw-semibold">No projects match your filters.</p>
                                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                @else
                                    <p class="mb-2 fw-semibold">No projects yet.</p>
                                    <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">Submit your first project</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Showing {{ $projects->firstItem() }}–{{ $projects->lastItem() }} of {{ $projects->total() }} projects
        </small>
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
