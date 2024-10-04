<?php

namespace App\Http\Resources\Messages;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'type' => $this->type,
            'filename' => $this->filename,
            'name' => $this->name
        ];
    }
}
