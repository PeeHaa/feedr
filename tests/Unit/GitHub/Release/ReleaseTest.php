<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub\Release;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Release\Release;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PHPUnit\Framework\TestCase;

class ReleaseTest extends TestCase
{
    /** @var Release */
    private $release;

    /** @var User */
    private $owner;

    /** @var Repository */
    private $repository;

    /** @var \DateTimeImmutable */
    private $timestamp;

    public function setUp()
    {
        $this->owner = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');

        $this->timestamp = new \DateTimeImmutable('@946684800');

        $this->repository = new Repository(
            21,
            'TestRepository',
            'TestUser1/TestRepository',
            'https://github.com/TestUser1/TestRepository',
            $this->owner
        );

        $this->release = new Release(
            19,
            'v0.5.4',
            'Release body',
            'https://github.com/release-url',
            $this->repository,
            $this->timestamp
        );
    }

    public function testGetId()
    {
        $this->assertSame(19, $this->release->getId());
    }

    public function testGetName()
    {
        $this->assertSame('v0.5.4', $this->release->getName());
    }

    public function testGetBody()
    {
        $this->assertSame('Release body', $this->release->getBody());
    }

    public function testGetUrl()
    {
        $this->assertSame('https://github.com/release-url', $this->release->getUrl());
    }

    public function testGetRepository()
    {
        $this->assertSame($this->repository, $this->release->getRepository());
    }

    public function testGetPublishedDate()
    {
        $this->assertSame($this->timestamp, $this->release->getPublishedDate());
    }

    public function testToArray()
    {
        $this->assertSame([
            'id'            => 19,
            'repository'    => [
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
            ],
            'name'          => 'v0.5.4',
            'body'          => 'Release body',
            'url'           => 'https://github.com/release-url',
            'publishedDate' => '946684800',
        ], $this->release->toArray());
    }
}
