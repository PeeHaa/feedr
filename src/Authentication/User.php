<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Authentication;

class User
{
    private $id;

    private $username;

    private $url;

    private $avatarUrl;

    public function __construct(int $id, string $username, string $url, string $avatarUrl)
    {
        $this->id        = $id;
        $this->username  = $username;
        $this->url       = $url;
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'username'  => $this->username,
            'url'       => $this->url,
            'avatarUrl' => $this->avatarUrl,
        ];
    }
}
