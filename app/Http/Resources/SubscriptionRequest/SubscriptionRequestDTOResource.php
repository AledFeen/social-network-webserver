<?php

namespace App\Http\Resources\SubscriptionRequest;

use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionRequestDTOResource extends JsonResource
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
            'created_at' => $this->getCreatedAt(),
        ];
    }
}
