<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Queue;

use Amp\Loop;
use Amp\Promise;
use PeeHaa\AwesomeFeed\GitHub\Repository as RepositoryEntity;
use PeeHaa\AwesomeFeed\Redis\Client as RedisClient;
use PeeHaa\AwesomeFeed\Storage\GitHub\Repository as RepositoryApi;
use PeeHaa\AwesomeFeed\Storage\Postgres\Release as ReleaseStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\Repository as RepositoryStorage;
use PeeHaa\AwesomeFeed\WebSocket\Controller;
use function Amp\call;

class Queue
{
    private $redisClient;

    private $repositoryStorage;

    private $releaseStorage;

    private $apiClient;

    private $controller;

    private $frequency;

    public function __construct(
        RedisClient $redisClient,
        RepositoryStorage $repositoryStorage,
        ReleaseStorage $releaseStorage,
        RepositoryApi $apiClient,
        Controller $controller,
        int $frequencyInSeconds = 2
    )
    {
        $this->redisClient       = $redisClient;
        $this->repositoryStorage = $repositoryStorage;
        $this->releaseStorage    = $releaseStorage;
        $this->apiClient         = $apiClient;
        $this->controller        = $controller;
        $this->frequency         = $frequencyInSeconds * 1000;
    }

    public function start(): void
    {
        Loop::run(function() {
            yield $this->redisClient->flush();
            yield $this->seedQueue();

            Loop::repeat($this->frequency, function() {
                $repository = yield $this->redisClient->popTask();

                if ($repository === null) {
                    return;
                }

                yield $this->processTask(RepositoryEntity::createFromArray(json_decode($repository, true)));
            });
        });
    }

    private function seedQueue(): Promise
    {
        return call(function() {
            foreach ($this->repositoryStorage->getAll() as $repository) {
                yield $this->redisClient->pushTask($repository);
            }
        });
    }

    private function processTask(RepositoryEntity $repository): Promise
    {
        return call(function() use ($repository) {
            $releases = yield $this->apiClient->getReleasesForRepository($repository);

            $this->releaseStorage->storeCollection($releases);

            yield $this->controller->pushReleases($repository, $releases);

            yield $this->redisClient->pushTask($repository);
        });
    }
}
