<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimeResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'anilist_id' => $this->anilist_id,
            
            // Loaded Relationships mappings
            'season' => $this->whenLoaded('season', fn() => ['id' => $this->season->id ?? null, 'name' => $this->season->name ?? null]),
            'year' => $this->whenLoaded('year', fn() => ['id' => $this->year->id ?? null, 'name' => $this->year->name ?? null]),
            'format' => $this->whenLoaded('format', fn() => ['id' => $this->format->id ?? null, 'name' => $this->format->name ?? null]),
            
            'studios' => $this->whenLoaded('studios'),
            'producers' => $this->whenLoaded('producers'),
            'genres' => $this->whenLoaded('genres'),
            'images' => $this->whenLoaded('images'),
            'externalLinks' => $this->whenLoaded('externalLinks'),
            
            'songs' => $this->whenLoaded('songs'),
            'songs_count' => $this->whenCounted('songs'),
            
            // Appended accessors or values assigned sequentially in Controller
            'thumbnail_url' => $this->thumbnail_url,
            'banner_url' => $this->banner_url,
            'average_rating' => $this->whenHas('average_rating'),
        ];
    }
}
