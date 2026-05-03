<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root { --sidebar-width: 240px; }
        body  { background: #f1f5f9; font-size: .9rem; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
            padding: 0; z-index: 100;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            color: #fff; font-weight: 700; font-size: 1.1rem;
            text-decoration: none; display: flex; align-items: center; gap: .6rem;
        }
        .sidebar-brand:hover { color: #fff; }
        .sidebar-brand .brand-icon {
            width: 32px; height: 32px; background: #3b82f6;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .sidebar-nav .nav-label {
            font-size: .65rem; font-weight: 600; letter-spacing: .08em;
            color: #64748b; text-transform: uppercase; padding: .5rem .75rem .25rem;
        }
        .sidebar-nav .nav-link {
            color: #94a3b8; border-radius: .5rem; padding: .55rem .75rem;
            display: flex; align-items: center; gap: .6rem; margin-bottom: 2px;
            transition: all .15s;
        }
        .sidebar-nav .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); }
        .sidebar-nav .nav-link.active { color: #fff; background: #3b82f6; }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.1rem; text-align: center; }
        .sidebar-footer {
            padding: .75rem; border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-footer .nav-link {
            color: #94a3b8; border-radius: .5rem; padding: .55rem .75rem;
            display: flex; align-items: center; gap: .6rem;
        }
        .sidebar-footer .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); }

        /* Main */
        .main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem; display: flex;
            align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-weight: 600; font-size: 1rem; color: #1e293b; }
        .main-content { padding: 1.5rem; }

        /* Cards */
        .stat-card { border: none; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .stat-card .icon-box {
            width: 48px; height: 48px; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
        }

        /* Table */
        .table th { font-size: .75rem; font-weight: 600; text-transform: uppercase;
                    letter-spacing: .05em; color: #64748b; }
        .card { border: none; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9;
                       font-weight: 600; padding: 1rem 1.25rem; }

        /* Badges */
        .badge { font-weight: 500; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-layers-fill text-white"></i></div>
        Project-Approval
    </a>

    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        @if(auth()->user()->isUser())
        <div class="nav-label mt-2">Projects</div>
        <a href="{{ route('projects.index') }}"
           class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}">
            <i class="bi bi-folder2-open"></i> My Projects
        </a>
        <a href="{{ route('projects.create') }}"
           class="nav-link {{ request()->routeIs('projects.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Submit Project
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-label mt-2">Admin</div>
        <a href="{{ route('admin.projects.index') }}"
           class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
            <i class="bi bi-kanban"></i> All Projects
        </a>
        <a href="{{ route('admin.audit-logs.index') }}"
           class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Audit Logs
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link w-100 text-start p-0 border-0">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main-wrapper">
    <div class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="d-flex align-items-center gap-3">
            <span class="badge rounded-pill {{ auth()->user()->isAdmin() ? 'bg-danger' : 'bg-primary' }} px-3 py-2">
                <i class="bi {{ auth()->user()->isAdmin() ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>
                {{ ucfirst(auth()->user()->role) }}
            </span>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:32px;height:32px;font-size:.8rem;flex-shrink:0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="text-dark fw-semibold small">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    <div class="main-content">
        {{-- Flash alerts --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
