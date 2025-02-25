<?php

namespace App\Http\Resources\Complaint;

use App\Http\Resources\Messages\MessageResource;
use App\Http\Resources\PostDTO\PostDTOResource;
use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => ComplaintResource::collection($this->data),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
