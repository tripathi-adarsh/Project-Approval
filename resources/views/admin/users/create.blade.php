@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')

<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus me-2 text-primary"></i>Create New User
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. John Doe" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="e.g. john@example.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select role...</option>
                    <option value="user"  {{ old('role') === 'user'  ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Min. 8 characters" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat password" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-person-check me-1"></i>Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
