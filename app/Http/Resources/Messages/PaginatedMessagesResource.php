<?php

namespace App\Http\Resources\Messages;

use App\Http\Resources\PostDTO\PostDTOResource;
use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedMessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => MessageResource::collection($this->data),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
