<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

use PeeHaa\AwesomeFeed\Authentication\User;

class Repository
{
    private $id;

    private $name;

    private $fullName;

    private $url;

    private $owner;

    public function __construct(int $id, string $name, string $fullName, string $url, User $owner)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->fullName = $fullName;
        $this->url      = $url;
        $this->owner    = $owner;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['fullName'],
            $data['url'],
            new User(
                $data['owner']['id'],
                $data['owner']['username'],
                $data['owner']['url'],
                $data['owner']['avatarUrl']
            )
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'fullName' => $this->fullName,
            'url'      => $this->url,
            'owner'    => $this->owner->toArray(),
        ];
    }
}
