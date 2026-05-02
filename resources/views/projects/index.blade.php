@extends('layouts.app')
@section('title', 'My Projects')
@section('page-title', 'My Projects')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Submit New Project
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    <tr>
                        <td class="text-muted">{{ $p->id }}</td>
                        <td class="fw-semibold">{{ $p->title }}</td>
                        <td><span class="badge {{ $p->statusBadgeClass() }}">{{ ucfirst($p->status) }}</span></td>
                        <td class="text-muted">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            @if($p->file_path)
                                <a href="{{ asset('storage/'.$p->file_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $p) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                            No projects yet.
                            <a href="{{ route('projects.create') }}">Submit your first project.</a>
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
@endsection
