<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'user_id' => $this->user_id,
            'real_name' => $this->real_name,
            'date_of_birth' => $this->date_of_birth,
            'about_me' => $this->about_me,
            'location' => $this->location,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
