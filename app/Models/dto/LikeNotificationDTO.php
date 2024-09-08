<?php

namespace App\Models\dto;

use Illuminate\Support\Carbon;

class LikeNotificationDTO
{
    protected int $id;
    protected UserDTO $user;
    protected int $postId;
    protected ?Carbon $createdAt;

    /**
     * @param int $id
     * @param UserDTO $user
     * @param int $postId
     * @param Carbon|null $createdAt
     */
    public function __construct(int $id, UserDTO $user, int $postId, ?Carbon $createdAt)
    {
        $this->id = $id;
        $this->user = $user;
        $this->postId = $postId;
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

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }
}
