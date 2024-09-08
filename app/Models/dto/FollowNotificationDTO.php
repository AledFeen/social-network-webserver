<?php

namespace App\Models\dto;

use Illuminate\Support\Carbon;

class FollowNotificationDTO
{
    protected int $id;
    protected UserDTO $follower;
    protected ?Carbon $createdAt;

    /**
     * @param int $id
     * @param UserDTO $follower
     * @param Carbon|null $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(int $id, UserDTO $follower, ?Carbon $createdAt)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->follower = $follower;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFollower(): UserDTO
    {
        return $this->follower;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

}
