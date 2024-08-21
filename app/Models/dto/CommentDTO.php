<?php

namespace App\Models\dto;

use App\Models\CommentFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CommentDTO
{
    protected int $id;
    protected int $post_id;
    protected int $user_id;
    protected int $hasReplies;
    protected string $text;
    protected Carbon $created_at;
    protected Carbon $updated_at;
    /**
     * @var CommentFile[] $files
     */
    protected Collection $files;

    // Конструктор
    public function __construct(
        int $id,
        int $post_id,
        int $user_id,
        string $text,
        Carbon $created_at,
        Carbon $updated_at,
        int $hasReplies,
        Collection $files
    ) {
        $this->id = $id;
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->text = $text;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->hasReplies = $hasReplies;
        $this->files = $files;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->post_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
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
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }
}
