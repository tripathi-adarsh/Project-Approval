<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body{background:linear-gradient(135deg,#1e3a8a 0%,#1e293b 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
        .auth-card{width:100%;max-width:440px;border:none;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,.3)}
        .brand-icon{width:48px;height:48px;background:#3b82f6;border-radius:12px;display:flex;align-items:center;justify-content:center}
    </style>
</head>
<body>
<div class="auth-card card p-4 p-md-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-icon"><i class="bi bi-layers-fill text-white fs-5"></i></div>
        <div>
            <h5 class="mb-0 fw-bold">Create Account</h5>
            <small class="text-muted">Join {{ config('app.name') }}</small>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Create Account</button>
    </form>

    <hr>
    <p class="text-center mb-0 small">
        Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
