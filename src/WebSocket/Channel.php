<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\WebSocket;

class Channel
{
    private $name;

    private $clients = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function subscribe(int $clientId): void
    {
        if (in_array($clientId, $this->clients, true)) {
            return;
        }

        $this->clients[] = $clientId;
    }

    public function unsubscribe(int $clientId): void
    {
        $key = array_search($clientId, $this->clients, true);

        if ($key === false) {
            return;
        }

        unset($this->clients[$clientId]);
    }

    public function getSubscribers(): array
    {
        return $this->clients;
    }
}
