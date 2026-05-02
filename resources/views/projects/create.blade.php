@extends('layouts.app')
@section('title', 'Submit Project')
@section('page-title', 'Submit New Project')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Project Details</div>
    <div class="card-body p-4">

        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" id="pForm">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="Enter project title" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                <textarea name="description" rows="5"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe your project in detail..." required>{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Max 5,000 characters.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Attachment <span class="text-muted fw-normal">(optional)</span></label>
                <input type="file" name="file"
                       class="form-control @error('file') is-invalid @enderror"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">PDF, DOC, DOCX, XLS, XLSX, ZIP — max 10 MB.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="spin"></span>
                    <i class="bi bi-send me-1"></i>Submit Project
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>

    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('pForm').addEventListener('submit', function(){
    document.getElementById('spin').classList.remove('d-none');
    document.getElementById('submitBtn').disabled = true;
});
</script>
@endpush
