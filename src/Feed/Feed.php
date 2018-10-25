<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Feed;

use PeeHaa\AwesomeFeed\Authentication\Collection as AdministratorCollection;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Collection as RepositoryCollection;
use PeeHaa\AwesomeFeed\GitHub\Repository;

class Feed
{
    private $id;

    private $name;

    private $slug;

    private $createdBy;

    private $administrators;

    private $repositories;

    public function __construct(
        int $id,
        string $name,
        string $slug,
        User $user,
        AdministratorCollection $administrators,
        RepositoryCollection $repositories
    )
    {
        $this->id             = $id;
        $this->name           = $name;
        $this->slug           = $slug;
        $this->createdBy      = $user;
        $this->administrators = $administrators;
        $this->repositories   = $repositories;
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

    public function getAdministrators(): AdministratorCollection
    {
        return $this->administrators;
    }

    public function hasUserAccess(User $user): bool
    {
        return $this->administrators->contains($user);
    }

    public function getRepositories(): RepositoryCollection
    {
        return $this->repositories;
    }

    public function isRepositoryAdded(Repository $repository): bool
    {
        return $this->repositories->contains($repository);
    }
}
