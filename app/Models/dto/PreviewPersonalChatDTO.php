<?php

namespace App\Models\dto;

use App\Models\CommentFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PreviewPersonalChatDTO
{
    protected int $id;
    protected string $type;
    protected UserDTO $user;
    protected int $countUnreadMessages;
    protected ?LastMessageDTO $lastMessage;

    /**
     * @param int $id
     * @param string $type
     * @param UserDTO $user
     * @param int $countUnreadMessages
     * @param LastMessageDTO|null $lastMessage
     */
    public function __construct(int $id, string $type, UserDTO $user, int $countUnreadMessages, ?LastMessageDTO $lastMessage)
    {
        $this->id = $id;
        $this->type = $type;
        $this->user = $user;
        $this->countUnreadMessages = $countUnreadMessages;
        $this->lastMessage = $lastMessage;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function getCountUnreadMessages(): int
    {
        return $this->countUnreadMessages;
    }

    public function getLastMessage(): ?LastMessageDTO
    {
        return $this->lastMessage;
    }
}
