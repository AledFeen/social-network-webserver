<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentNotificationDTOResource extends JsonResource
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
            'user' => new UserDTOResource($this->getUser()),
            'post_id' => $this->getPostId(),
            'text' => $this->getCommentText(),
            'created_at' => $this->getCreatedAt()
        ];
    }
}
