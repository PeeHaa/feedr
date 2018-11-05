<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub\Release;

use PeeHaa\AwesomeFeed\GitHub\Repository;

class Release
{
    private $id;

    private $name;

    private $body;

    private $url;

    private $repository;

    private $publishedDate;

    public function __construct(
        int $id,
        string $name,
        string $body,
        string $url,
        Repository $repository,
        \DateTimeImmutable $publishedDate
    ) {
        $this->id            = $id;
        $this->name          = $name;
        $this->body          = $body;
        $this->url           = $url;
        $this->repository    = $repository;
        $this->publishedDate = $publishedDate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function getPublishedDate(): \DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'repository'    => $this->repository->toArray(),
            'name'          => $this->name,
            'body'          => $this->body,
            'url'           => $this->url,
            'publishedDate' => $this->publishedDate->format('U'),
        ];
    }
}
