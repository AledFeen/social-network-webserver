<?php

namespace App\Models\dto;

use Illuminate\Support\Carbon;

class SubscriptionRequestDTO
{
    protected int $id;
    protected UserDTO $user;
    protected ?Carbon $createdAt;

    /**
     * @param int $id
     * @param UserDTO $user
     * @param Carbon|null $createdAt
     */
    public function __construct(int $id, UserDTO $user, ?Carbon $createdAt)
    {
        $this->id = $id;
        $this->user = $user;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }
}
