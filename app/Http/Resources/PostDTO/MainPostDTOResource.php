<?php

namespace App\Http\Resources\PostDTO;


use App\Http\Resources\TagResource;
use App\Http\Resources\UserDTO\UserDTOResource;
use App\Models\CommentFile;
use Illuminate\Http\Resources\Json\JsonResource;

class MainPostDTOResource extends JsonResource
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
            'location' => $this->getLocation(),
            'text' => $this->getText(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'tags' => TagResource::collection($this->getTags()),
            'files' => PostFileResource::collection($this->getFiles()),
        ];
    }
}
