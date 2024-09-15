<?php

namespace App\Models\dto;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class ProfileDTO
{
    protected int $id;
    protected string $name;
    protected string $image;
    protected ?Date $birthday;
    protected ?string $about;
    protected ?string $realName;
    protected ?string $location;
    protected string $accountType;
    protected string $whoCanMessage;

    /**
     * @param int $id
     * @param string $name
     * @param string $image
     * @param Date|null $birthday
     * @param string|null $about
     * @param string|null $realName
     * @param string|null $location
     * @param string $accountType
     * @param string $whoCanMessage
     */
    public function __construct(int $id, string $name, string $image, ?Date $birthday, ?string $about, ?string $realName, ?string $location, string $accountType, string $whoCanMessage)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->birthday = $birthday;
        $this->about = $about;
        $this->realName = $realName;
        $this->location = $location;
        $this->accountType = $accountType;
        $this->whoCanMessage = $whoCanMessage;
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

    public function getBirthday(): ?Date
    {
        return $this->birthday;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function getRealName(): ?string
    {
        return $this->realName;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function getWhoCanMessage(): string
    {
        return $this->whoCanMessage;
    }


}
