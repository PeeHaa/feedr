<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Authentication\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var MockObject|User */
    private $user1;
    
    /** @var MockObject|User */
    private $user2;
    
    /** @var MockObject|User */
    private $user3;
    
    /** @var MockObject|Collection */
    private $collection;

    public function setUp()
    {
        $this->user1 = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar/png');
        $this->user2 = new User(14, 'TestUser2', 'https://github.com/TestUser2', 'https://github.com/avatar/png');
        $this->user3 = new User(15, 'TestUser3', 'https://github.com/TestUser3', 'https://github.com/avatar/png');
        $this->collection = new Collection;
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
        $this->assertSame(true, $this->collection->valid());
    }

    public function testValidWitouthUser()
    {
        $this->assertSame(false, $this->collection->valid());
    }

    public function testRewind()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);
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
        $this->assertSame(true, $this->collection->contains($this->user1));
    }

    public function testContainsWithoutUser()
    {
        $this->assertSame(false, $this->collection->contains($this->user1));
    }

    public function testFilter()
    {
        $this->collection->add($this->user1);
        $this->collection->add($this->user2);
        $this->collection->add($this->user3);

        $filteredCollection = $this->collection->filter(function(User $user) {
            return $user->getId() === 13;
        });
        
        $this->assertSame(13, $filteredCollection->key());
    }

    public function testToArray()
    {
        $this->collection->add($this->user1);

        $this->assertSame([
            13 => [
                'id'        => 13,
                'username'  => 'TestUser1',
                'url'       => 'https://github.com/TestUser1',
                'avatarUrl' => 'https://github.com/avatar/png',
            ]
        ], $this->collection->toArray());
    }

}
