<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
.wrap{max-width:580px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.hdr{padding:24px 32px;color:#fff}
.hdr.submitted{background:#3b82f6}
.hdr.approved{background:#22c55e}
.hdr.rejected{background:#ef4444}
.body{padding:32px}
.badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:700;color:#fff}
.badge.submitted{background:#3b82f6}
.badge.approved{background:#22c55e}
.badge.rejected{background:#ef4444}
.footer{background:#f8f9fa;padding:16px 32px;font-size:12px;color:#6c757d}
table{width:100%;border-collapse:collapse;margin:16px 0}
td{padding:10px 0;border-bottom:1px solid #f1f5f9}
td:first-child{color:#64748b;width:130px}
.btn{display:inline-block;background:#3b82f6;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:8px}
</style>
</head>
<body>
<div class="wrap">
    <div class="hdr {{ $event }}">
        <h2 style="margin:0">
            @if($event==='submitted') 📋 Project Submitted
            @elseif($event==='approved') ✅ Project Approved
            @else ❌ Project Rejected
            @endif
        </h2>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $project->user->name }}</strong>,</p>

        @if($event==='submitted')
            <p>Your project has been submitted and is awaiting admin review.</p>
        @elseif($event==='approved')
            <p>Great news! Your project has been <strong>approved</strong>.</p>
        @else
            <p>Your project has been <strong>rejected</strong>. Please see the reason below.</p>
        @endif

        <table>
            <tr><td>Project</td><td><strong>{{ $project->title }}</strong></td></tr>
            <tr><td>Status</td><td><span class="badge {{ $event }}">{{ ucfirst($project->status) }}</span></td></tr>
            @if($event==='rejected' && $project->latestApproval?->reason)
            <tr><td>Reason</td><td>{{ $project->latestApproval->reason }}</td></tr>
            @endif
            <tr><td>Date</td><td>{{ now()->format('d M Y, H:i') }}</td></tr>
        </table>

        <a href="{{ route('projects.show', $project) }}" class="btn">View Project</a>
    </div>
    <div class="footer">Automated message from {{ config('app.name') }}. Do not reply.</div>
</div>
</body>
</html>
