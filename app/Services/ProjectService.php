<?php

namespace App\Services;

use App\Jobs\SendProjectNotification;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function create(User $user, array $data, ?UploadedFile $file): Project
    {
        $filePath = $originalName = null;

        if ($file) {
            $filePath     = $file->store('projects', 'public');
            $originalName = $file->getClientOriginalName();
        }

        $project = $user->projects()->create([
            'title'              => $data['title'],
            'description'        => $data['description'],
            'file_path'          => $filePath,
            'file_original_name' => $originalName,
            'status'             => 'pending',
        ]);

        $this->log('project_submitted', $user->id, $project->id);
        SendProjectNotification::dispatch($project, 'submitted');

        return $project;
    }

    public function approve(User $admin, Project $project): bool
    {
        DB::statement('CALL sp_approve_project(?, ?, @result)', [$project->id, $admin->id]);
        $outcome = DB::select('SELECT @result AS result')[0]->result ?? 0;

        if (! $outcome) return false;

        $project->refresh();
        SendProjectNotification::dispatch($project, 'approved');
        return true;
    }

    public function reject(User $admin, Project $project, string $reason): void
    {
        DB::transaction(function () use ($admin, $project, $reason) {
            $project->update(['status' => 'rejected']);

            $project->approvals()->create([
                'admin_id' => $admin->id,
                'status'   => 'rejected',
                'reason'   => $reason,
            ]);

            $this->log('project_rejected', $admin->id, $project->id, ['reason' => $reason]);
        });

        SendProjectNotification::dispatch($project->fresh(), 'rejected');
    }

    public function bulkApprove(User $admin, array $ids): int
    {
        $count = 0;
        foreach ($ids as $id) {
            $project = Project::find($id);
            if ($project && $project->isPending() && $this->approve($admin, $project)) {
                $count++;
            }
        }
        return $count;
    }

    public function bulkReject(User $admin, array $ids, string $reason): int
    {
        $count = 0;
        foreach ($ids as $id) {
            $project = Project::find($id);
            if ($project && $project->isPending()) {
                $this->reject($admin, $project, $reason);
                $count++;
            }
        }
        return $count;
    }

    private function log(string $action, int $userId, int $projectId, array $meta = []): void
    {
        AuditLog::create([
            'action'     => $action,
            'user_id'    => $userId,
            'project_id' => $projectId,
            'meta'       => $meta ?: null,
        ]);
    }
}
