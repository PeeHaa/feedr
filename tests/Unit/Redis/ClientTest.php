<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Redis;

use Amp\Loop;
use Amp\Promise;
use Amp\Redis\Client as AmpClient;
use Amp\Success;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PeeHaa\AwesomeFeed\Redis\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /** @var Repository */
    private $repository;

    private $repositoryArray = [];

    public function setUp()
    {
        $this->repository = new Repository(
            21,
            'TestRepository',
            'TestUser1/TestRepository',
            'https://github.com/TestUser1/TestRepository',
            new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png')
        );

        $this->repositoryArray = [
            'id'       => 21,
            'name'     => 'TestRepository',
            'fullName' => 'TestUser1/TestRepository',
            'url'      => 'https://github.com/TestUser1/TestRepository',
            'owner'    => [
                'id'        => 13,
                'username'  => 'TestUser1',
                'url'       => 'https://github.com/TestUser1',
                'avatarUrl' => 'https://github.com/avatar1.png',
            ],
        ];
    }

    public function testFlush()
    {
        /** @var MockObject|AmpClient $redisClient */
        $redisClient = $this->createMock(AmpClient::class);

        $redisClient
            ->expects($this->once())
            ->method('query')
            ->willReturn(new Success(['foo', 'bar']))
            ->with('KEYS', 'FEEDR_*')
        ;

        $redisClient
            ->expects($this->at(0))
            ->method('del')
            ->willReturnCallback(function($key) {
                $this->assertSame('foo', $key);

                return new Success();
            })
        ;

        $redisClient
            ->expects($this->at(1))
            ->method('del')
            ->willReturnCallback(function($key) {
                $this->assertSame('bar', $key);

                return new Success();
            })
        ;

        $client = new Client($redisClient);

        Loop::run(function() use ($client) {
            $client->flush();
        });
    }

    public function testPushTask()
    {
        $this->repositoryArray['once'] = false;

        /** @var MockObject|AmpClient $redisClient */
        $redisClient = $this->createMock(AmpClient::class);

        $redisClient
            ->expects($this->once())
            ->method('query')
            ->willReturnCallback(function($command, $key, $data) {
                $this->assertSame(json_encode($this->repositoryArray), $data);

                return new Success();
            })
            ->with('RPUSH', 'FEEDR_repository')
        ;

        $client = new Client($redisClient);

        Loop::run(function() use ($client) {
            $client->pushTask($this->repository);
        });
    }

    public function testPushTaskToFront()
    {
        $this->repositoryArray['once'] = false;

        /** @var MockObject|AmpClient $redisClient */
        $redisClient = $this->createMock(AmpClient::class);

        $redisClient
            ->expects($this->once())
            ->method('query')
            ->willReturnCallback(function($command, $key, $data) {
                $this->assertSame(json_encode($this->repositoryArray), $data);

                return new Success();
            })
            ->with('LPUSH', 'FEEDR_repository')
        ;

        $client = new Client($redisClient);

        Loop::run(function() use ($client) {
            $client->pushTaskToFront($this->repository);
        });
    }

    public function testPushTaskToFrontOnce()
    {
        $this->repositoryArray['once'] = true;

        /** @var MockObject|AmpClient $redisClient */
        $redisClient = $this->createMock(AmpClient::class);

        $redisClient
            ->expects($this->once())
            ->method('query')
            ->willReturnCallback(function($command, $key, $data) {
                $this->assertSame(json_encode($this->repositoryArray), $data);

                return new Success();
            })
            ->with('LPUSH', 'FEEDR_repository')
        ;

        $client = new Client($redisClient);

        Loop::run(function() use ($client) {
            $client->pushTaskToFrontOnce($this->repository);
        });
    }

    public function testPopTask()
    {
        /** @var MockObject|AmpClient $redisClient */
        $redisClient = $this->createMock(AmpClient::class);

        $redisClient
            ->expects($this->once())
            ->method('query')
            ->willReturn(new Success())
            ->with('LPOP', 'FEEDR_repository')
        ;

        $client = new Client($redisClient);

        Loop::run(function() use ($client) {
            $client->popTask();
        });
    }
}
