<?php

namespace App\Jobs;

use App\Mail\ProjectStatusMail;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendProjectNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Project $project,
        public readonly string  $event,
    ) {}

    public function handle(): void
    {
        $this->project->loadMissing(['user', 'latestApproval']);

        Mail::to($this->project->user->email)
            ->send(new ProjectStatusMail($this->project, $this->event));
    }
}
