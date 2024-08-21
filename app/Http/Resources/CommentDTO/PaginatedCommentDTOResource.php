<?php

namespace App\Http\Resources\CommentDTO;

use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedCommentDTOResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => CommentDTOResource::collection($this->data),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
