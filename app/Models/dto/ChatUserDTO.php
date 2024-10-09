<?php

namespace App\Models\dto;

use App\Models\CommentFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ChatUserDTO
{
    protected int $id;
    protected UserDTO $user;

    /**
     * @param int $id
     * @param UserDTO $user
     */
    public function __construct(int $id, UserDTO $user)
    {
        $this->id = $id;
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }
}
