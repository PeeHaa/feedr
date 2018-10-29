<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\WebSocket;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Endpoint;
use Amp\Http\Server\Websocket\Message;
use Amp\Promise;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use function Amp\call;

class Controller implements Application
{
    /** @var null|Endpoint */
    private $endpoint;

    /** @var Channels */
    private $channels;

    public function onStart(Endpoint $endpoint): void
    {
        $this->endpoint = $endpoint;
        $this->channels = new Channels();
    }

    public function onHandshake(Request $request, Response $response): Response
    {
        return $response;
    }

    public function onOpen(int $clientId, Request $request): void
    {
        // nothing here
    }

    public function onData(int $clientId, Message $message)
    {
        $command = json_decode(yield $message->read(), true);

        switch ($command['command']) {
            case 'subscribe':
                $this->channels->subscribeToChannel($clientId, $command['channel']);
                break;

            case 'unsubscribe':
                $this->channels->unsubscribeFromChannel($clientId, $command['channel']);
                break;
        }
    }

    public function pushReleases(Repository $repository, Collection $releases): Promise
    {
        return call(function() use ($repository, $releases) {
            yield $this->endpoint->multicast(json_encode([
                'command'  => 'newReleases',
                'releases' => $releases->toArray(),
            ]), $this->channels->getSubscribersOfChannel($repository->getFullName()));
        });
    }

    public function onClose(int $clientId, int $code, string $reason): void
    {
        $this->channels->unsubscribeClient($clientId);
    }

    public function onStop(): void
    {
        // nothing here
    }
}
