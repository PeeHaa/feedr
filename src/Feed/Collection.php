<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Feed;

class Collection implements \Iterator, \Countable
{
    private $feeds = [];

    public function add(Feed $feed): void
    {
        $this->feeds[] = $feed;
    }

    public function current(): Feed
    {
        return current($this->feeds);
    }

    public function next(): void
    {
        next($this->feeds);
    }

    public function key(): ?int
    {
        return key($this->feeds);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->feeds);
    }

    public function count()
    {
        return count($this->feeds);
    }
}
