<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description',
        'file_path', 'file_original_name', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function latestApproval()
    {
        return $this->hasOne(Approval::class)->latestOfMany();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
    public function scopeRejected($q)  { return $q->where('status', 'rejected'); }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default    => 'bg-warning text-dark',
        };
    }
}
