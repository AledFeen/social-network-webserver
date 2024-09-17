<?php

namespace App\Http\Resources\SubscriptionRequest;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedSubRequestDTOResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => SubscriptionRequestDTOResource::collection($this->data),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
