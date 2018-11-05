<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Feed;

use PeeHaa\AwesomeFeed\Authentication\Collection as UserCollection;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\GitHub\Collection as RepositoryCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection as ReleaseCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Release;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PHPUnit\Framework\TestCase;

class FeedTest extends TestCase
{
    /** @var Feed */
    private $feed;

    /** @var User */
    private $createdBy;

    /** @var UserCollection */
    private $administrators;

    /** @var RepositoryCollection */
    private $repositories;

    /** @var Repository */
    private $repository;

    /** @var ReleaseCollection */
    private $releases;

    /** @var Release */
    private $release;

    public function setUp()
    {
        $this->createdBy      = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');
        $this->administrators = new UserCollection();
        $this->releases       = new ReleaseCollection();
        $this->repository     = new Repository(
            21,
            'TestRepository',
            'TestUser1/TestRepository',
            'https://github.com/TestUser1/TestRepository',
            $this->createdBy
        );
        $this->repositories   = new RepositoryCollection();
        $this->release        = new Release(
            19,
            'v0.5.4',
            'Release body',
            'https://github.com/release-url',
            $this->repository,
            new \DateTimeImmutable('@946684800')
        );

        $this->feed = new Feed(
            13,
            'Test Feed',
            'test-feed',
            $this->createdBy,
            $this->administrators,
            $this->repositories,
            $this->releases
        );
    }

    public function testGetId()
    {
        $this->assertSame(13, $this->feed->getId());
    }

    public function testGetName()
    {
        $this->assertSame('Test Feed', $this->feed->getName());
    }

    public function testGetSlug()
    {
        $this->assertSame('test-feed', $this->feed->getSlug());
    }

    public function testGetCreatedBy()
    {
        $this->assertSame($this->createdBy, $this->feed->getCreatedBy());
    }

    public function testGetAdministrators()
    {
        $this->assertSame($this->administrators, $this->feed->getAdministrators());
    }

    public function testHasUserAccessWhenAllowed()
    {
        $this->administrators->add($this->createdBy);

        $this->assertTrue($this->feed->hasUserAccess($this->createdBy));
    }

    public function testHasUserAccessWhenNotAllowed()
    {
        $this->assertFalse($this->feed->hasUserAccess($this->createdBy));
    }

    public function testGetRepositories()
    {
        $this->assertSame($this->repositories, $this->feed->getRepositories());
    }

    public function testIsRepositoryAddedWhenAdded()
    {
        $this->repositories->add($this->repository);

        $this->assertTrue($this->feed->isRepositoryAdded($this->repository));
    }

    public function testIsRepositoryAddedWhenNotAdded()
    {
        $this->assertFalse($this->feed->isRepositoryAdded($this->repository));
    }

    public function testGetReleases()
    {
        $this->assertSame($this->releases, $this->feed->getReleases());
    }

    public function testToArray()
    {
        $this->administrators->add($this->createdBy);
        $this->repositories->add($this->repository);
        $this->releases->add($this->release);

        $this->assertSame([
            'id'        => 13,
            'name'      => 'Test Feed',
            'slug'      => 'test-feed',
            'createdBy' => [
                'id'        => 13,
                'username'  => 'TestUser1',
                'url'       => 'https://github.com/TestUser1',
                'avatarUrl' => 'https://github.com/avatar1.png',
            ],
            'administrators' => [
                13 => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
            ],
            'repositories' => [
                21 => [
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
            ],
            'releases' => [
                19 => [
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
                ],
            ],
        ], $this->feed->toArray());
    }
}
