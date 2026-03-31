<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'content' => $this->content,
            'status' => (bool) $this->status,
            'source' => $this->source,
            'reported_user' => [
                'id' => $this->reportedUser->id,
                'name' => $this->reportedUser->name,
                'slug' => $this->reportedUser->slug,
                'avatar_url' => $this->reportedUser->avatar_url,
            ],
            'reporter_user' => [
                'id' => $this->reporterUser->id,
                'name' => $this->reporterUser->name,
                'slug' => $this->reporterUser->slug,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
