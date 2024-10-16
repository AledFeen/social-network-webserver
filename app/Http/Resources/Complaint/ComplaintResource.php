<?php

namespace App\Http\Resources\Complaint;

use App\Http\Resources\Messages\MessageFileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'comment_id' => $this->comment_id,
            'message_id' => $this->message_id,
            'text' => $this->text,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'measure' => $this->measure,
            'measure_status' => $this->measure_status
        ];
    }
}
