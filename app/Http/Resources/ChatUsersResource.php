<?php

namespace App\Http\Resources;


use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatUsersResource extends JsonResource
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
            'user' => new UserDTOResource($this->getUser()),
        ];
    }
}
