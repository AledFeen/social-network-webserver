<?php

namespace App\Models\dto;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;


class PostDTO
{
    protected int $id;
    protected UserDTO $user;
    protected ?int $repostId;
    protected ?string $location;
    protected ?string $text;
    protected ?Carbon $createdAt;
    protected ?Carbon $updatedAt;

    protected int $repostCount;
    protected int $likeCount;
    protected int $commentCount;
    protected Collection $tags;
    protected Collection $files;

    protected ?MainPostDTO $mainPost;

    /**
     * @param int $id
     * @param UserDTO $user
     * @param int|null $repostId
     * @param string|null $location
     * @param string|null $text
     * @param Carbon|null $createdAt
     * @param Carbon|null $updatedAt
     * @param int $repostCount
     * @param int $likeCount
     * @param int $commentCount
     * @param Collection $tags
     * @param Collection $files
     * @param MainPostDTO|null $mainPost
     */
    public function __construct(
        int $id, UserDTO $user, ?int $repostId,
        ?string $location, ?string $text,
        ?Carbon $createdAt, ?Carbon $updatedAt,
        int $repostCount, int $likeCount, int $commentCount,
        Collection $tags, Collection $files, ?MainPostDTO $mainPost)
    {
        $this->id = $id;
        $this->user = $user;
        $this->repostId = $repostId;
        $this->location = $location;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->repostCount = $repostCount;
        $this->likeCount = $likeCount;
        $this->commentCount = $commentCount;
        $this->tags = $tags;
        $this->files = $files;
        $this->mainPost = $mainPost;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function getRepostId(): ?int
    {
        return $this->repostId;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function getRepostCount(): int
    {
        return $this->repostCount;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function getMainPost(): ?MainPostDTO
    {
        return $this->mainPost;
    }

}
