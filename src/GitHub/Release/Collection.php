<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub\Release;

class Collection implements \Iterator, \Countable
{
    private $releases = [];

    public function add(Release $release): void
    {
        $this->releases[$release->getId()] = $release;
    }

    public function current(): Release
    {
        return current($this->releases);
    }

    public function next()
    {
        next($this->releases);
    }

    public function key(): ?int
    {
        return key($this->releases);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->releases);
    }

    public function count(): int
    {
        return count($this->releases);
    }

    public function contains(Release $release): bool
    {
        return array_key_exists($release->getId(), $this->releases);
    }

    public function filter(callable $callback): self
    {
        $collection = new Collection();

        foreach ($this->releases as $release) {
            if ($callback($release) === true) {
                $collection->add($release);
            }
        }

        return $collection;
    }

    public function toArray(): array
    {
        $releases = [];

        foreach ($this->releases as $release) {
            $releases[$release->getId()] = $release->toArray();
        }

        return $releases;
    }
}
