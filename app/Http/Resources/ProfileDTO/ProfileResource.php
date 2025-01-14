<?php

namespace App\Http\Resources\ProfileDTO;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name' => $this->getName(),
            'image' => $this->getImage(),
            'birthday' => $this->getBirthday(),
            'about' => $this->getAbout(),
            'realName' => $this->getRealName(),
            'location' => $this->getLocation(),
            'accountType' => $this->getAccountType(),
            'whoCanMessage' => $this->getWhoCanMessage(),
            'countFollowers' => $this->getCountFollowers(),
            'countFollowings' => $this->getCountFollowings()
        ];
    }
}
