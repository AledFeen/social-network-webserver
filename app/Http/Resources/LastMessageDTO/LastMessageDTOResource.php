<?php

namespace App\Http\Resources\LastMessageDTO;


use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LastMessageDTOResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->getId(),
            'link_id' => $this->getLinkId(),
            'is_read' => $this->getIsRead(),
            'text' => $this->getText(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'user' => new UserDTOResource($this->getUser()),
        ];
    }
}
