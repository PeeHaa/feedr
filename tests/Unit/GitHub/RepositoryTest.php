<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /** @var User */
    private $owner;

    /** @var Repository */
    private $repository;

    public function setUp()
    {
        $this->owner = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');

        $this->repository = new Repository(
            21,
            'TestRepository',
            'TestUser1/TestRepository',
            'https://github.com/TestUser1/TestRepository',
            $this->owner
        );
    }

    public function testGetId()
    {
        $this->assertSame(21, $this->repository->getId());
    }

    public function testGetName()
    {
        $this->assertSame('TestRepository', $this->repository->getName());
    }

    public function testGetFullName()
    {
        $this->assertSame('TestUser1/TestRepository', $this->repository->getFullName());
    }

    public function testGetUrl()
    {
        $this->assertSame('https://github.com/TestUser1/TestRepository', $this->repository->getUrl());
    }

    public function testGetOwner()
    {
        $this->assertSame($this->owner, $this->repository->getOwner());
    }

    public function testToArray()
    {
        $this->assertSame([
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
        ], $this->repository->toArray());
    }

    public function testCreateFromArray()
    {
        $data = [
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

        $this->assertSame($data, Repository::createFromArray($data)->toArray());
    }
}
