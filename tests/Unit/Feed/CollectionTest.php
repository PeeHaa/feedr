<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Feed;

use PeeHaa\AwesomeFeed\Authentication\Collection as UserCollection;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Collection;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\GitHub\Collection as RepositoryCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection as ReleaseCollection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var Feed */
    private $feed1;
    
    /** @var Feed */
    private $feed2;
    
    /** @var Feed */
    private $feed3;
    
    /** @var Collection */
    private $collection;

    public function setUp()
    {
        $createdBy = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');

        $this->feed1 = new Feed(
            13,
            'Test Feed 1',
            'test-feed-1',
            $createdBy,
            new UserCollection(),
            new RepositoryCollection(),
            new ReleaseCollection()
        );

        $this->feed2 = new Feed(
            14,
            'Test Feed 2',
            'test-feed-2',
            $createdBy,
            new UserCollection(),
            new RepositoryCollection(),
            new ReleaseCollection()
        );

        $this->feed3 = new Feed(
            15,
            'Test Feed 3',
            'test-feed-3',
            $createdBy,
            new UserCollection(),
            new RepositoryCollection(),
            new ReleaseCollection()
        );

        $this->collection = new Collection();
    }

    public function addRemovesDuplicates()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed1);

        $this->assertSame(2, $this->collection->count());
    }

    public function testCurrent()
    {
        $this->collection->add($this->feed1);

        $this->assertSame($this->feed1, $this->collection->current());
    }

    public function testNext()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $this->collection->next();

        $this->assertSame($this->feed2, $this->collection->current());
    }

    public function testKey()
    {
        $this->collection->add($this->feed1);

        $this->assertSame(13, $this->collection->key());
    }

    public function testValidWithFeed()
    {
        $this->collection->add($this->feed1);

        $this->assertTrue($this->collection->valid());
    }

    public function testValidWithoutFeed()
    {
        $this->assertFalse($this->collection->valid());
    }

    public function testValidWhenFinishedIterating()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $this->collection->next();
        $this->collection->next();
        $this->collection->next();

        $this->assertFalse($this->collection->valid());
    }

    public function testRewind()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $this->collection->next();

        $this->collection->rewind();

        $this->collection->next();

        $this->assertSame($this->feed2, $this->collection->current());
    }

    public function testCount()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $this->assertSame(3, $this->collection->count());
    }

    public function testFilterIsImmutable()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $filteredCollection = $this->collection->filter(static function(Feed $feed) {
            return $feed->getId() === 14;
        });

        $this->assertNotSame($filteredCollection->key(), $this->collection);
    }

    public function testFilterFilters()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $filteredCollection = $this->collection->filter(static function(Feed $feed) {
            return $feed->getId() === 14;
        });
        
        $this->assertSame(1, $filteredCollection->count());

        $this->assertFalse($filteredCollection->contains($this->feed1));
        $this->assertFalse($filteredCollection->contains($this->feed3));

        $this->assertTrue($filteredCollection->contains($this->feed2));
    }

    public function testToArray()
    {
        $this->collection->add($this->feed1);

        $this->assertSame([
            13 => [
                'id'        => 13,
                'name'      => 'Test Feed 1',
                'slug'      => 'test-feed-1',
                'createdBy' => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
                'administrators' => [],
                'repositories' => [],
                'releases' => [],
            ],
        ], $this->collection->toArray());
    }

    public function testToArrayWithMultipleFeeds()
    {
        $this->collection->add($this->feed1);
        $this->collection->add($this->feed2);
        $this->collection->add($this->feed3);

        $this->assertSame([
            13 => [
                'id'        => 13,
                'name'      => 'Test Feed 1',
                'slug'      => 'test-feed-1',
                'createdBy' => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
                'administrators' => [],
                'repositories' => [],
                'releases' => [],
            ],
            14 => [
                'id'        => 14,
                'name'      => 'Test Feed 2',
                'slug'      => 'test-feed-2',
                'createdBy' => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
                'administrators' => [],
                'repositories' => [],
                'releases' => [],
            ],
            15 => [
                'id'        => 15,
                'name'      => 'Test Feed 3',
                'slug'      => 'test-feed-3',
                'createdBy' => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
                'administrators' => [],
                'repositories' => [],
                'releases' => [],
            ],
        ], $this->collection->toArray());
    }
}
