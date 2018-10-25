<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

class Collection implements \Iterator, \Countable
{
    private $repositories = [];

    public function add(Repository $repository): void
    {
        $this->repositories[$repository->getId()] = $repository;
    }

    public function current(): Repository
    {
        return current($this->repositories);
    }

    public function next(): void
    {
        next($this->repositories);
    }

    public function key(): ?int
    {
        return key($this->repositories);
    }


    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->repositories);
    }

    public function count(): int
    {
        return count($this->repositories);
    }

    public function contains(Repository $repository): bool
    {
        return array_key_exists($repository->getId(), $this->repositories);
    }

    public function filter(callable $callback): self
    {
        $collection = new Collection();

        foreach ($this->repositories as $repository) {
            if ($callback($repository) === true) {
                $collection->add($repository);
            }
        }

        return $collection;
    }

    public function toArray(): array
    {
        $repositories = [];

        foreach ($this->repositories as $repository) {
            $repositories[$repository->getId()] = $repository->toArray();
        }

        return $repositories;
    }
}
