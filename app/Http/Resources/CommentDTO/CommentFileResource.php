<?php

namespace App\Http\Resources\CommentDTO;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentFileResource extends JsonResource
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
            'comment_id' => $this->comment_id,
            'type' => $this->type,
            'filename' => $this->filename
        ];
    }
}
