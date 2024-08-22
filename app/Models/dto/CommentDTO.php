<?php

namespace App\Models\dto;

use App\Models\CommentFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CommentDTO
{
    protected int $id;
    protected int $postId;
    protected int $userId;
    protected string $text;
    protected Carbon $createdAt;
    protected Carbon $updatedAt;
    protected int $hasReplies;
    protected Collection $files;

    /**
     * @param int $id
     * @param int $post_id
     * @param int $user_id
     * @param string $text
     * @param Carbon $created_at
     * @param Carbon $updated_at
     * @param int $hasReplies
     * @param Collection $files
     */
    public function __construct(
        int $id, int $post_id, int $user_id, string $text,
        Carbon $created_at, Carbon $updated_at, int $hasReplies,
        Collection $files)
    {
        $this->id = $id;
        $this->postId = $post_id;
        $this->userId = $user_id;
        $this->text = $text;
        $this->createdAt = $created_at;
        $this->updatedAt = $updated_at;
        $this->hasReplies = $hasReplies;
        $this->files = $files;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function hasReplies(): int
    {
        return $this->hasReplies;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }
}
