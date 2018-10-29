<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Redis;

use Amp\Promise;
use Amp\Redis\Client as AmpClient;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use function Amp\call;

class Client
{
    private const PREFIX = 'FEEDR_';

    private $client;

    public function __construct(AmpClient $client)
    {
        $this->client = $client;
    }

    public function flush(): Promise
    {
        return call(function() {
            $keys = yield $this->client->query('KEYS', $this->buildKey('*'));

            foreach ($keys as $key) {
                yield $this->client->del($key);
            }
        });
    }

    public function pushTask(Repository $repository): Promise
    {
        return $this->client->query('RPUSH', $this->buildKey('repository'), json_encode($repository->toArray() + ['once' => false]));
    }

    public function pushTaskToFront(Repository $repository): Promise
    {
        return $this->client->query('LPUSH', $this->buildKey('repository'), json_encode($repository->toArray() + ['once' => false]));
    }

    public function pushTaskToFrontOnce(Repository $repository): Promise
    {
        return $this->client->query('LPUSH', $this->buildKey('repository'), json_encode($repository->toArray() + ['once' => true]));
    }

    public function popTask(): Promise
    {
        return $this->client->query('LPOP', $this->buildKey('repository'));
    }

    public function __call($name, $arguments): Promise
    {
        return $this->client->$name(...$arguments);
    }

    private function buildKey(string $name): string
    {
        return self::PREFIX . $name;
    }
}
