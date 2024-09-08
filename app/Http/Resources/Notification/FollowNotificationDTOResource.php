<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowNotificationDTOResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'follower' => new UserDTOResource($this->getFollower()),
            'created_at' => $this->getCreatedAt(),
        ];
    }
}
