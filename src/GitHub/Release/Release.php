<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub\Release;

class Release
{
    private $id;

    private $repositoryId;

    private $name;

    private $body;

    private $url;

    private $publishedDate;

    public function __construct(
        int $id,
        int $repositoryId,
        string $name,
        string $body,
        string $url,
        \DateTimeImmutable $publishedDate
    )
    {
        $this->id            = $id;
        $this->repositoryId  = $repositoryId;
        $this->name          = $name;
        $this->body          = $body;
        $this->url           = $url;
        $this->publishedDate = $publishedDate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRepositoryId(): int
    {
        return $this->repositoryId;
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

    public function getPublishedDate(): \DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'repositoryId'  => $this->repositoryId,
            'name'          => $this->name,
            'body'          => $this->body,
            'url'           => $this->url,
            'publishedDate' => $this->publishedDate,
        ];
    }
}
