<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\WebSocket;

class Channels
{
    /** @var Channel[] */
    private $channels = [];

    public function subscribeToChannel(int $clientId, string $channel): void
    {
        if (!array_key_exists($channel, $this->channels)) {
            $this->channels[$channel] = new Channel($channel);
        }

        $this->channels[$channel]->subscribe($clientId);
    }

    public function unsubscribeFromChannel(int $clientId, string $channel): void
    {
        if (!array_key_exists($channel, $this->channels)) {
            return;
        }

        $this->channels[$channel]->unsubscribe($clientId);
    }

    public function unsubscribeClient(int $clientId): void
    {
        foreach ($this->channels as $channel) {
            $channel->unsubscribe($clientId);
        }
    }

    public function getSubscribersOfChannel(string $channel): array
    {
        if (!array_key_exists($channel, $this->channels)) {
            return [];
        }

        return $this->channels[$channel]->getSubscribers();
    }
}
