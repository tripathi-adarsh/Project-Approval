@extends('layouts.app')
@section('title', 'Manage Projects')
@section('page-title', 'All Projects')

@section('content')

{{-- Filters (standalone GET form — no nesting issue) --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Project title...">
            </div>
            <div class="col-sm-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-sm-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
            <div class="col-sm-1">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Bulk action toolbar (outside the table, no nested forms) --}}
<div class="d-flex gap-2 mb-2 align-items-center flex-wrap">
    <select class="form-select form-select-sm w-auto" id="bulkAction">
        <option value="">Bulk Action</option>
        <option value="approve">Approve Selected</option>
        <option value="reject">Reject Selected</option>
    </select>
    <input type="text" id="bulkReason"
           class="form-control form-control-sm w-auto d-none"
           placeholder="Rejection reason (required)">
    <button class="btn btn-sm btn-warning" onclick="submitBulk()">Apply</button>
    <span class="text-muted small ms-auto">{{ $projects->total() }} project(s) found</span>
</div>

{{-- Table (no forms inside — actions handled via JS hidden forms) --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    <tr>
                        <td>
                            @if($p->isPending())
                            <input type="checkbox" value="{{ $p->id }}"
                                   class="form-check-input row-check">
                            @endif
                        </td>
                        <td class="text-muted">{{ $p->id }}</td>
                        <td>
                            <a href="{{ route('projects.show', $p) }}"
                               class="fw-semibold text-decoration-none text-dark">
                                {{ Str::limit($p->title, 40) }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $p->user->name }}</td>
                        <td>
                            <span class="badge {{ $p->statusBadgeClass() }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="text-muted">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            @if($p->isPending())
                            {{-- Approve: JS submits a hidden PATCH form --}}
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="approveProject({{ $p->id }})"
                                    title="Approve">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            {{-- Reject: opens modal --}}
                            <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                    data-id="{{ $p->id }}" data-title="{{ $p->title }}"
                                    title="Reject">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @else
                            <a href="{{ route('projects.show', $p) }}"
                               class="btn btn-sm btn-outline-secondary">View</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>No projects found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
    <div class="card-footer bg-white">{{ $projects->links() }}</div>
    @endif
</div>

{{-- ── Hidden forms (rendered outside table, submitted via JS) ── --}}

{{-- Approve form (PATCH) --}}
<form method="POST" id="approveForm" class="d-none">
    @csrf
    <input type="hidden" name="_method" value="PATCH">
</form>

{{-- Bulk form (POST) --}}
<form method="POST" action="{{ route('admin.projects.bulk') }}" id="bulkForm" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <input type="hidden" name="reason" id="bulkReasonInput">
    {{-- ids injected dynamically --}}
</form>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="rejectForm" class="d-none">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <textarea name="reason" id="rejectReasonHidden" class="d-none"></textarea>
        </form>

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject: <span id="modalTitle" class="text-danger"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                <textarea id="rejectReasonInput" rows="4" class="form-control"
                          placeholder="Explain the rejection reason..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">Confirm Reject</button>
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
});

function submitReject() {
    const reason = document.getElementById('rejectReasonInput').value.trim();
    if (!reason) {
        document.getElementById('rejectReasonInput').classList.add('is-invalid');
        return;
    }
    document.getElementById('rejectReasonHidden').value = reason;
    document.getElementById('rejectForm').submit();
}
</script>
@endpush
