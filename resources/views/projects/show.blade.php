@extends('layouts.app')
@section('title', $project->title)
@section('page-title', 'Project Details')

@section('content')
<div class="row g-3">

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $project->title }}</span>
                <span class="badge {{ $project->statusBadgeClass() }} fs-6">{{ ucfirst($project->status) }}</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Submitted by <strong>{{ $project->user->name }}</strong>
                    on {{ $project->created_at->format('d M Y, H:i') }}
                </p>
                <p class="mb-3">{{ $project->description }}</p>

                @if($project->file_path)
                <a href="{{ asset('storage/'.$project->file_path) }}" target="_blank"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-paperclip me-1"></i>{{ $project->file_original_name }}
                </a>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        @if($project->auditLogs->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-clock-history me-2 text-primary"></i>Activity Timeline</div>
            <ul class="list-group list-group-flush">
                @foreach($project->auditLogs->sortByDesc('created_at') as $log)
                <li class="list-group-item py-3">
                    <div class="d-flex justify-content-between">
                        <span>
                            <i class="bi bi-circle-fill text-primary me-2" style="font-size:.5rem;vertical-align:middle"></i>
                            <strong>{{ str_replace('_',' ',ucfirst($log->action)) }}</strong>
                            by {{ $log->user->name }}
                        </span>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                    @if(!empty($log->meta['reason']))
                    <p class="ms-3 mb-0 text-muted small mt-1">Reason: {{ $log->meta['reason'] }}</p>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Admin review panel --}}
        @if(auth()->user()->isAdmin() && $project->isPending())
        <div class="card border-warning mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-shield-check me-2"></i>Review Project
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.projects.approve', $project) }}" class="mb-2">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success w-100"
                            onclick="return confirm('Approve this project?')">
                        <i class="bi bi-check-lg me-1"></i>Approve
                    </button>
                </form>
                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-lg me-1"></i>Reject
                </button>
            </div>
        </div>
        @endif

        {{-- Decision card --}}
        @if($project->latestApproval)
        <div class="card">
            <div class="card-header">Decision</div>
            <div class="card-body">
                <span class="badge {{ $project->statusBadgeClass() }} mb-2">
                    {{ ucfirst($project->latestApproval->status) }}
                </span>
                <p class="text-muted small mb-1">by {{ $project->latestApproval->admin->name }}</p>
                <p class="text-muted small">{{ $project->latestApproval->created_at->format('d M Y, H:i') }}</p>
                @if($project->latestApproval->reason)
                <hr>
                <p class="mb-0 small"><strong>Reason:</strong> {{ $project->latestApproval->reason }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
@if(auth()->user()->isAdmin() && $project->isPending())
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.projects.reject', $project) }}">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" rows="4"
                              class="form-control @error('reason') is-invalid @enderror"
                              placeholder="Explain why this project is rejected..." required></textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
