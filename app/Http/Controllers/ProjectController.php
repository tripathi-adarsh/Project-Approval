<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private ProjectService $service) {}

    public function index(Request $request)
    {
        $projects = $request->user()->projects()->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $project = $this->service->create(
            $request->user(),
            $request->validated(),
            $request->file('file')
        );

        return redirect()->route('projects.index')
            ->with('success', "Project \"{$project->title}\" submitted successfully.");
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['user', 'latestApproval.admin', 'auditLogs.user']);
        return view('projects.show', compact('project'));
    }

    public function adminIndex(Request $request)
    {
        $query = Project::with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $projects = $query->paginate(15)->withQueryString();
        $users    = User::where('role', 'user')->get();

        return view('admin.projects.index', compact('projects', 'users'));
    }

    public function approve(Request $request, Project $project)
    {
        $this->authorize('approve', $project);

        $ok  = $this->service->approve($request->user(), $project);
        $msg = $ok ? "Project \"{$project->title}\" approved." : 'Could not approve (already processed?).';

        return back()->with($ok ? 'success' : 'error', $msg);
    }

    public function reject(RejectProjectRequest $request, Project $project)
    {
        $this->authorize('reject', $project);

        $this->service->reject($request->user(), $project, $request->input('reason'));

        return back()->with('success', "Project \"{$project->title}\" rejected.");
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer'],
            'reason' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ]);

        $user   = $request->user();
        $action = $request->input('action');
        $ids    = $request->input('ids');

        $count = $action === 'approve'
            ? $this->service->bulkApprove($user, $ids)
            : $this->service->bulkReject($user, $ids, $request->input('reason', ''));

        return back()->with('success', "{$count} project(s) {$action}d.");
    }
}
