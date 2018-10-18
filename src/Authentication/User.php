<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Authentication;

class User
{
    private $id;

    private $username;

    private $avatarUrl;

    public function __construct(int $id, string $username, string $avatarUrl)
    {
        $this->id        = $id;
        $this->username  = $username;
        $this->avatarUrl = $avatarUrl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}
