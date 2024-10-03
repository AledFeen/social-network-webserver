<?php

namespace App\Http\Resources\PreviewChatDTO;


use App\Http\Resources\LastMessageDTO\LastMessageDTOResource;
use App\Http\Resources\UserDTO\UserDTOResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewChatDTOResource extends JsonResource
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
            'type' => $this->getType(),
            'user' => new UserDTOResource($this->getUser()),
            'count_unread' => $this->getCountUnreadMessages(),
            'last_message' => new LastMessageDTOResource($this->getLastMessage()),
        ];
    }
}
