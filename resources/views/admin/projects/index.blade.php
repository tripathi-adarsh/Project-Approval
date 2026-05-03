@extends('layouts.app')
@section('title', 'Manage Projects')
@section('page-title', 'All Projects')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold text-dark">All Projects</h5>
        <small class="text-muted">Review and manage submitted projects</small>
    </div>
    <span class="badge bg-primary rounded-pill fs-6 px-3">{{ $projects->total() }} Total</span>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-4">
                <label class="form-label small fw-semibold mb-1 text-muted">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           value="{{ request('search') }}" placeholder="Search by title...">
                </div>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1 text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
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
                @if(request()->hasAny(['search','status','from','to']))
                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Active filter badges --}}
@if(request()->hasAny(['search','status','from','to']))
<div class="d-flex flex-wrap gap-2 mb-3">
    @if(request('search'))
    <span class="badge bg-light text-dark border">
        <i class="bi bi-search me-1"></i>{{ request('search') }}
    </span>
    @endif
    @if(request('status'))
    <span class="badge bg-light text-dark border">
        <i class="bi bi-circle-fill me-1 {{ request('status') === 'approved' ? 'text-success' : (request('status') === 'rejected' ? 'text-danger' : 'text-warning') }}" style="font-size:.5rem"></i>
        {{ ucfirst(request('status')) }}
    </span>
    @endif
    @if(request('from'))
    <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>From: {{ request('from') }}</span>
    @endif
    @if(request('to'))
    <span class="badge bg-light text-dark border"><i class="bi bi-calendar me-1"></i>To: {{ request('to') }}</span>
    @endif
</div>
@endif

{{-- Bulk action toolbar --}}
<div class="d-flex gap-2 mb-2 align-items-center flex-wrap">
    <select class="form-select form-select-sm w-auto" id="bulkAction">
        <option value="">Bulk Action</option>
        <option value="approve">Approve Selected</option>
        <option value="reject">Reject Selected</option>
    </select>
    <input type="text" id="bulkReason"
           class="form-control form-control-sm w-auto d-none"
           placeholder="Rejection reason (required)">
    <button class="btn btn-sm btn-warning" onclick="submitBulk()">
        <i class="bi bi-lightning-fill me-1"></i>Apply
    </button>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th style="width:50px">#</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    <tr>
                        <td>
                            @if($p->isPending())
                            <input type="checkbox" value="{{ $p->id }}" class="form-check-input row-check">
                            @endif
                        </td>
                        <td class="text-muted small">{{ $p->id }}</td>
                        <td>
                            <a href="{{ route('projects.show', $p) }}"
                               class="fw-semibold text-decoration-none text-dark">
                                {{ Str::limit($p->title, 45) }}
                            </a>
                            @if($p->file_path)
                            <i class="bi bi-paperclip text-muted ms-1" title="Has attachment"></i>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                     style="width:28px;height:28px;font-size:.7rem;flex-shrink:0">
                                    {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                </div>
                                <span class="text-muted small">{{ $p->user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $p->statusBadgeClass() }}">
                                <i class="bi {{ $p->status === 'approved' ? 'bi-check-circle' : ($p->status === 'rejected' ? 'bi-x-circle' : 'bi-hourglass-split') }} me-1"></i>
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            @if($p->isPending())
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="approveProject({{ $p->id }})" title="Approve">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                    data-id="{{ $p->id }}" data-title="{{ $p->title }}"
                                    title="Reject">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @else
                            <a href="{{ route('projects.show', $p) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                @if(request()->hasAny(['search','status','from','to']))
                                    <p class="mb-2 fw-semibold">No projects match your filters.</p>
                                    <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                @else
                                    <p class="mb-0 fw-semibold">No projects found.</p>
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
        {{ $projects->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Hidden forms --}}
<form method="POST" id="approveForm" class="d-none">
    @csrf
    <input type="hidden" name="_method" value="PATCH">
</form>

<form method="POST" action="{{ route('admin.projects.bulk') }}" id="bulkForm" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="reason" id="bulkReasonInput">
</form>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm" class="d-none">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <textarea name="reason" id="rejectReasonHidden" class="d-none"></textarea>
        </form>
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Reject Project</h5>
                    <p class="text-muted small mb-0" id="modalTitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <label class="form-label fw-semibold small">Reason <span class="text-danger">*</span></label>
                <textarea id="rejectReasonInput" rows="4" class="form-control"
                          placeholder="Explain why this project is being rejected..."></textarea>
                <div class="invalid-feedback d-none" id="rejectError">Reason is required.</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" onclick="submitReject()">
                    <i class="bi bi-x-lg me-1"></i>Confirm Reject
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});

document.getElementById('bulkAction').addEventListener('change', function () {
    const r = document.getElementById('bulkReason');
    r.classList.toggle('d-none', this.value !== 'reject');
    r.required = this.value === 'reject';
});

function approveProject(id) {
    if (!confirm('Approve this project?')) return;
    const form = document.getElementById('approveForm');
    form.action = `/admin/projects/${id}/approve`;
    form.submit();
}

function submitBulk() {
    const checked = [...document.querySelectorAll('.row-check:checked')];
    if (!checked.length) { alert('Select at least one project.'); return; }

    const action = document.getElementById('bulkAction').value;
    if (!action) { alert('Choose a bulk action.'); return; }

    const reason = document.getElementById('bulkReason').value;
    if (action === 'reject' && !reason.trim()) {
        alert('Rejection reason is required.');
        return;
    }

    if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} ${checked.length} project(s)?`)) return;

    const form = document.getElementById('bulkForm');
    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkReasonInput').value = reason;
    form.submit();
}

document.getElementById('rejectModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('rejectForm').action = `/admin/projects/${btn.dataset.id}/reject`;
    document.getElementById('modalTitle').textContent = btn.dataset.title;
    document.getElementById('rejectReasonInput').value = '';
    document.getElementById('rejectReasonInput').classList.remove('is-invalid');
    document.getElementById('rejectError').classList.add('d-none');
});

function submitReject() {
    const reason = document.getElementById('rejectReasonInput').value.trim();
    if (!reason) {
        document.getElementById('rejectReasonInput').classList.add('is-invalid');
        document.getElementById('rejectError').classList.remove('d-none');
        return;
    }
    document.getElementById('rejectReasonHidden').value = reason;
    document.getElementById('rejectForm').submit();
}
</script>
@endpush
