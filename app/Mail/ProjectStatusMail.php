<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Project $project,
        public readonly string  $event,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'submitted' => "Project Submitted: {$this->project->title}",
            'approved'  => "✅ Project Approved: {$this->project->title}",
            'rejected'  => "❌ Project Rejected: {$this->project->title}",
        ];

        return new Envelope(subject: $subjects[$this->event] ?? "Project Update");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.project-status');
    }
}
