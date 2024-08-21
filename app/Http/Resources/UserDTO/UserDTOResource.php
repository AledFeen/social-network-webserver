<?php

namespace App\Http\Resources\UserDTO;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDTOResource extends JsonResource
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
            'name' => $this->getName(),
            'image' => $this->getImage(),
        ];
    }
}
