<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\WebSocket;

class Configuration
{
    private $internalHostname;

    private $internalPort;

    private $expose;

    public function __construct(array $configuration)
    {
        $this->internalHostname = $configuration['webSocket']['internalHostname'];
        $this->internalPort     = $configuration['webSocket']['internalPort'];
        $this->expose           = $configuration['webSocket']['expose'];
    }

    public function getInternalHostname(): string
    {
        return $this->internalHostname;
    }

    public function getInternalPort(): int
    {
        return $this->internalPort;
    }

    public function exposeInternalAddress(): bool
    {
        return $this->expose;
    }

    public function __toString(): string
    {
        return json_encode([
            'internalHostname'      => $this->internalHostname,
            'internalPort'          => $this->getInternalPort(),
            'exposeInternalAddress' => $this->expose,
        ]);
    }
}
