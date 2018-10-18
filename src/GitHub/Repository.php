<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

class Repository
{
    private $owner;

    private $name;

    public function __construct(string $owner, string $name)
    {
        $this->owner = $owner;
        $this->name  = $name;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
