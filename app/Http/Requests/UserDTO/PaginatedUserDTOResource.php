<?php

namespace App\Http\Requests\UserDTO;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedUserDTOResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => UserDTOResource::collection($this->data),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
