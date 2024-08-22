<?php

namespace App\Models\dto;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MainPostDTO
{
    protected int $id;
    protected UserDTO $user;
    protected ?string $location;
    protected ?string $text;
    protected ?Carbon $createdAt;
    protected ?Carbon $updatedAt;
    protected Collection $tags;
    protected Collection $files;

    /**
     * @param int $id
     * @param UserDTO $user
     * @param string|null $location
     * @param string|null $text
     * @param Carbon|null $createdAt
     * @param Carbon|null $updatedAt
     * @param Collection $tags
     * @param Collection $files
     */
    public function __construct(
        int $id, UserDTO $user,
        ?string $location, ?string $text,
        ?Carbon $createdAt, ?Carbon $updatedAt,
        Collection $tags, Collection $files)
    {
        $this->id = $id;
        $this->user = $user;
        $this->location = $location;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->tags = $tags;
        $this->files = $files;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
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


    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

}
