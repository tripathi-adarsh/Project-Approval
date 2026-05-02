<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private ProjectService $service) {}

    /** POST /api/projects */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->service->create(
            $request->user(),
            $request->validated(),
            $request->file('file')
        );

        return response()->json([
            'message' => 'Project submitted.',
            'data'    => new ProjectResource($project),
        ], 201);
    }

    /** PATCH /api/projects/{project}/approve */
    public function approve(Request $request, Project $project): JsonResponse
    {
        $this->authorize('approve', $project);

        $ok = $this->service->approve($request->user(), $project);

        if (! $ok) {
            return response()->json(['message' => 'Could not approve project.'], 422);
        }

        return response()->json([
            'message' => 'Project approved.',
            'data'    => new ProjectResource($project->fresh()),
        ]);
    }
}
