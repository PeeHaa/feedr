<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Authentication;

class User
{
    private $id;

    private $username;

    public function __construct(int $id, string $username)
    {
        $this->id       = $id;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
