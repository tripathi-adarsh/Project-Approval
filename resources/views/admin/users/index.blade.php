@extends('layouts.app')
@section('title', 'Manage Users')
@section('page-title', 'Users')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold text-dark">All Users</h5>
        <small class="text-muted">Manage system users and their roles</small>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i> Add User
    </a>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5">
                <label class="form-label small fw-semibold mb-1 text-muted">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           value="{{ request('search') }}" placeholder="Name or email...">
                </div>
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1 text-muted">Role</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="col-sm-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-3">
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
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Projects</th>
                        <th>Joined</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted small">{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0
                                    {{ $user->isAdmin() ? 'bg-danger' : 'bg-primary' }}"
                                     style="width:36px;height:36px;font-size:.85rem">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                    <span class="badge bg-light text-muted border" style="font-size:.65rem">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $user->isAdmin() ? 'bg-danger' : 'bg-primary' }}">
                                <i class="bi {{ $user->isAdmin() ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $user->projects_count }}</span>
                            <span class="text-muted small">project{{ $user->projects_count !== 1 ? 's' : '' }}</span>
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete user {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete user">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                                @if(request()->hasAny(['search','role']))
                                    <p class="mb-2 fw-semibold">No users match your filters.</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                                @else
                                    <p class="mb-0 fw-semibold">No users found.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
        </small>
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
