<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\Collection;
use PeeHaa\AwesomeFeed\Authentication\User;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var User */
    private $user1;
    
    /** @var User */
    private $user2;
    
    /** @var User */
    private $user3;
    
    /** @var Collection */
    private $collection;

    public function setUp()
    {
        $this->user1 = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');
        $this->user2 = new User(14, 'TestUser2', 'https://github.com/TestUser2', 'https://github.com/avatar2.png');
        $this->user3 = new User(15, 'TestUser3', 'https://github.com/TestUser3', 'https://github.com/avatar2.png');

        $this->collection = new Collection();
    }

    public function addRemovesDuplicates()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user1);

        $this->assertSame(2, $this->collection->count());
    }

    public function testCurrent()
    {
        $this->collection->add($this->user1);

        $this->assertSame($this->user1, $this->collection->current());
    }

    public function testNext()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $this->collection->next();

        $this->assertSame($this->user2, $this->collection->current());
    }

    public function testKey()
    {
        $this->collection->add($this->user1);

        $this->assertSame(13, $this->collection->key());
    }

    public function testValidWithUser()
    {
        $this->collection->add($this->user1);

        $this->assertTrue($this->collection->valid());
    }

    public function testValidWithoutUser()
    {
        $this->assertFalse($this->collection->valid());
    }

    public function testValidWhenFinishedIterating()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $this->collection->next();
        $this->collection->next();
        $this->collection->next();

        $this->assertFalse($this->collection->valid());
    }

    public function testRewind()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $this->collection->next();

        $this->collection->rewind();

        $this->collection->next();

        $this->assertSame($this->user2, $this->collection->current());
    }

    public function testCount()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $this->assertSame(3, $this->collection->count());
    }

    public function testContainsWithUser()
    {
        $this->collection->add($this->user1);

        $this->assertTrue($this->collection->contains($this->user1));
    }

    public function testContainsWithoutUser()
    {
        $this->assertFalse($this->collection->contains($this->user1));
    }

    public function testContainsWithUserWithMatch()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);

        $this->assertTrue($this->collection->contains($this->user2));
    }

    public function testFilterIsImmutable()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $filteredCollection = $this->collection->filter(static function(User $user) {
            return $user->getId() === 14;
        });

        $this->assertNotSame($filteredCollection->key(), $this->collection);
    }

    public function testFilterFilters()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $filteredCollection = $this->collection->filter(static function(User $user) {
            return $user->getId() === 14;
        });
        
        $this->assertSame(1, $filteredCollection->count());

        $this->assertFalse($filteredCollection->contains($this->user1));
        $this->assertFalse($filteredCollection->contains($this->user3));

        $this->assertTrue($filteredCollection->contains($this->user2));
    }

    public function testToArray()
    {
        $this->collection->add($this->user1);

        $this->assertSame([
            13 => [
                'id'        => 13,
                'username'  => 'TestUser1',
                'url'       => 'https://github.com/TestUser1',
                'avatarUrl' => 'https://github.com/avatar1.png',
            ],
        ], $this->collection->toArray());
    }

    public function testToArrayWithMultipleUsers()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);

        $this->assertSame([
            13 => [
                'id'        => 13,
                'username'  => 'TestUser1',
                'url'       => 'https://github.com/TestUser1',
                'avatarUrl' => 'https://github.com/avatar1.png',
            ],
            14 => [
                'id'        => 14,
                'username'  => 'TestUser2',
                'url'       => 'https://github.com/TestUser2',
                'avatarUrl' => 'https://github.com/avatar2.png',
            ],
        ], $this->collection->toArray());
    }
}
