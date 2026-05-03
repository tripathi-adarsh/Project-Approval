@extends('layouts.app')
@section('title', $project->title)
@section('page-title', 'Project Details')

@section('content')

{{-- Back button --}}
<div class="mb-3">
    <a href="{{ auth()->user()->isAdmin() ? route('admin.projects.index') : route('projects.index') }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Projects
    </a>
</div>

<div class="row g-3">

    {{-- Main content --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-start gap-2">
                <div>
                    <h6 class="mb-1 fw-bold">{{ $project->title }}</h6>
                    <small class="text-muted">
                        Submitted by <strong>{{ $project->user->name }}</strong>
                        · {{ $project->created_at->format('d M Y, H:i') }}
                    </small>
                </div>
                <span class="badge rounded-pill {{ $project->statusBadgeClass() }} fs-6 flex-shrink-0">
                    <i class="bi {{ $project->status === 'approved' ? 'bi-check-circle' : ($project->status === 'rejected' ? 'bi-x-circle' : 'bi-hourglass-split') }} me-1"></i>
                    {{ ucfirst($project->status) }}
                </span>
            </div>
            <div class="card-body">
                <p class="mb-4 lh-lg">{{ $project->description }}</p>

                @if($project->file_path)
                <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $project->file_original_name }}</div>
                        <div class="text-muted" style="font-size:.75rem">Attached file</div>
                    </div>
                    <a href="{{ asset('storage/'.$project->file_path) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Activity Timeline --}}
        @if($project->auditLogs->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-clock-history me-2 text-primary"></i>Activity Timeline
            </div>
            <div class="card-body p-0">
                @foreach($project->auditLogs->sortByDesc('created_at') as $log)
                <div class="d-flex gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        @php
                            $dot = str_contains($log->action, 'approved') ? 'bg-success'
                                : (str_contains($log->action, 'rejected') ? 'bg-danger' : 'bg-primary');
                        @endphp
                        <div class="rounded-circle {{ $dot }}" style="width:10px;height:10px;margin-top:4px"></div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold small">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                <span class="text-muted small"> by {{ $log->user->name }}</span>
                            </div>
                            <small class="text-muted flex-shrink-0 ms-2">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!empty($log->meta['reason']))
                        <p class="mb-0 text-muted small mt-1">
                            <i class="bi bi-chat-left-text me-1"></i>{{ $log->meta['reason'] }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">

        {{-- Admin review panel --}}
        @if(auth()->user()->isAdmin() && $project->isPending())
        <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #f59e0b !important;">
            <div class="card-header" style="background:#fffbeb;border-bottom:1px solid #fde68a">
                <i class="bi bi-shield-check me-2 text-warning"></i>
                <span class="fw-semibold">Review Project</span>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <form method="POST" action="{{ route('admin.projects.approve', $project) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success w-100"
                            onclick="return confirm('Approve this project?')">
                        <i class="bi bi-check-lg me-1"></i>Approve Project
                    </button>
                </form>
                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-lg me-1"></i>Reject Project
                </button>
            </div>
        </div>
        @endif

        {{-- Decision card --}}
        @if($project->latestApproval)
        <div class="card">
            <div class="card-header"><i class="bi bi-clipboard-check me-2 text-primary"></i>Decision</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill {{ $project->statusBadgeClass() }} fs-6">
                        {{ ucfirst($project->latestApproval->status) }}
                    </span>
                </div>
                <div class="text-muted small mb-1">
                    <i class="bi bi-person me-1"></i>by <strong>{{ $project->latestApproval->admin->name }}</strong>
                </div>
                <div class="text-muted small">
                    <i class="bi bi-calendar me-1"></i>{{ $project->latestApproval->created_at->format('d M Y, H:i') }}
                </div>
                @if($project->latestApproval->reason)
                <hr>
                <div class="small">
                    <div class="fw-semibold mb-1"><i class="bi bi-chat-left-text me-1"></i>Reason</div>
                    <p class="mb-0 text-muted">{{ $project->latestApproval->reason }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Project info card --}}
        <div class="card {{ $project->latestApproval ? 'mt-3' : '' }}">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Project Info</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted small">ID</span>
                        <span class="small fw-semibold">#{{ $project->id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Submitted</span>
                        <span class="small">{{ $project->created_at->format('d M Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Last Updated</span>
                        <span class="small">{{ $project->updated_at->diffForHumans() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span class="text-muted small">Attachment</span>
                        <span class="small">{{ $project->file_path ? 'Yes' : 'No' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
@if(auth()->user()->isAdmin() && $project->isPending())
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.projects.reject', $project) }}">
            @csrf @method('PATCH')
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Reject Project</h5>
                        <p class="text-muted small mb-0">{{ Str::limit($project->title, 50) }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <label class="form-label fw-semibold small">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" rows="4"
                              class="form-control @error('reason') is-invalid @enderror"
                              placeholder="Explain why this project is being rejected..." required></textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-x-lg me-1"></i>Confirm Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
