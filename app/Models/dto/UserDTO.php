<?php

namespace App\Models\dto;

class UserDTO
{
    protected int $id;
    protected string $name;
    protected string $image;

    /**
     * @param $id
     * @param $name
     * @param $image
     */
    public function __construct(int $id, string $name, string $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
