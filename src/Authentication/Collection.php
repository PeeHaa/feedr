<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Authentication;

class Collection implements \Iterator, \Countable
{
    private $users;

    public function add(User $user): void
    {
        $this->users[] = $user;
    }

    public function current(): User
    {
        return current($this->users);
    }

    public function next(): void
    {
        next($this->users);
    }

    public function key(): ?int
    {
        return key($this->users);
    }


    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->users);
    }

    public function count()
    {
        return count($this->users);
    }
}
