<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'status'       => $this->status,
            'file_url'     => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'submitted_by' => ['id' => $this->user->id, 'name' => $this->user->name],
            'created_at'   => $this->created_at->toIso8601String(),
        ];
    }
}
