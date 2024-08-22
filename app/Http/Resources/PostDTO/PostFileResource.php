<?php

namespace App\Http\Resources\PostDTO;

use Illuminate\Http\Resources\Json\JsonResource;

class PostFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'type' => $this->type,
            'filename' => $this->filename
        ];
    }
}
