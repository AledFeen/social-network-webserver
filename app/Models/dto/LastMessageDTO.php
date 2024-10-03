<?php

namespace App\Models\dto;

use App\Models\CommentFile;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class LastMessageDTO
{
    protected int $id;
    protected int $link_id;
    protected bool $is_read;
    protected ?Carbon $createdAt;
    protected ?Carbon $updatedAt;
    protected UserDTO $user;

    /**
     * @param int $id
     * @param int $link_id
     * @param bool $is_read
     * @param Carbon|null $createdAt
     * @param Carbon|null $updatedAt
     * @param UserDTO $user
     */
    public function __construct(int $id, int $link_id, bool $is_read, ?Carbon $createdAt, ?Carbon $updatedAt, UserDTO $user)
    {
        $this->id = $id;
        $this->link_id = $link_id;
        $this->is_read = $is_read;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLinkId(): int
    {
        return $this->link_id;
    }

    public function getIsRead(): bool
    {
        return $this->is_read;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }
}
