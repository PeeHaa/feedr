<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Feed;

use PeeHaa\AwesomeFeed\Authentication\Collection;
use PeeHaa\AwesomeFeed\Authentication\User;

class Feed
{
    private $id;

    private $name;

    private $slug;

    private $createdBy;

    private $administrators;

    public function __construct(int $id, string $name, string $slug, User $user, Collection $administrators)
    {
        $this->id             = $id;
        $this->name           = $name;
        $this->slug           = $slug;
        $this->createdBy      = $user;
        $this->administrators = $administrators;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getAdministrators(): Collection
    {
        return $this->administrators;
    }
}
