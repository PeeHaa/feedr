<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Collection;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var Repository */
    private $repository1;

    /** @var Repository */
    private $repository2;

    /** @var Repository */
    private $repository3;

    /** @var Collection */
    private $collection;

    public function setUp()
    {
        $createdBy = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');

        $this->repository1 = new Repository(
            21,
            'TestRepository1',
            'TestUser1/TestRepository1',
            'https://github.com/TestUser1/TestRepository1',
            $createdBy
        );

        $this->repository2 = new Repository(
            22,
            'TestRepository2',
            'TestUser1/TestRepository2',
            'https://github.com/TestUser1/TestRepository2',
            $createdBy
        );

        $this->repository3 = new Repository(
            23,
            'TestRepository3',
            'TestUser1/TestRepository3',
            'https://github.com/TestUser1/TestRepository3',
            $createdBy
        );

        $this->collection = new Collection();
    }

    public function addRemovesDuplicates()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository1);

        $this->assertSame(2, $this->collection->count());
    }

    public function testCurrent()
    {
        $this->collection->add($this->repository1);

        $this->assertSame($this->repository1, $this->collection->current());
    }

    public function testNext()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $this->collection->next();

        $this->assertSame($this->repository2, $this->collection->current());
    }

    public function testKey()
    {
        $this->collection->add($this->repository1);

        $this->assertSame(21, $this->collection->key());
    }

    public function testValidWithUser()
    {
        $this->collection->add($this->repository1);

        $this->assertTrue($this->collection->valid());
    }

    public function testValidWithoutUser()
    {
        $this->assertFalse($this->collection->valid());
    }

    public function testValidWhenFinishedIterating()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $this->collection->next();
        $this->collection->next();
        $this->collection->next();

        $this->assertFalse($this->collection->valid());
    }

    public function testRewind()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $this->collection->next();

        $this->collection->rewind();

        $this->collection->next();

        $this->assertSame($this->repository2, $this->collection->current());
    }

    public function testCount()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $this->assertSame(3, $this->collection->count());
    }

    public function testContainsWithUser()
    {
        $this->collection->add($this->repository1);

        $this->assertTrue($this->collection->contains($this->repository1));
    }

    public function testContainsWithoutUser()
    {
        $this->assertFalse($this->collection->contains($this->repository1));
    }

    public function testContainsWithUserWithMatch()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);

        $this->assertTrue($this->collection->contains($this->repository2));
    }

    public function testFilterIsImmutable()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $filteredCollection = $this->collection->filter(static function(Repository $repository) {
            return $repository->getId() === 22;
        });

        $this->assertNotSame($filteredCollection->key(), $this->collection);
    }

    public function testFilterFilters()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);
        $this->collection->add($this->repository3);

        $filteredCollection = $this->collection->filter(static function(Repository $repository) {
            return $repository->getId() === 22;
        });

        $this->assertSame(1, $filteredCollection->count());

        $this->assertFalse($filteredCollection->contains($this->repository1));
        $this->assertFalse($filteredCollection->contains($this->repository3));

        $this->assertTrue($filteredCollection->contains($this->repository2));
    }

    public function testToArray()
    {
        $this->collection->add($this->repository1);

        $this->assertSame([
            21 => [
                'id'       => 21,
                'name'     => 'TestRepository1',
                'fullName' => 'TestUser1/TestRepository1',
                'url'      => 'https://github.com/TestUser1/TestRepository1',
                'owner'    => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
            ],
        ], $this->collection->toArray());
    }

    public function testToArrayWithMultipleRepositories()
    {
        $this->collection->add($this->repository1);
        $this->collection->add($this->repository2);

        $this->assertSame([
            21 => [
                'id'       => 21,
                'name'     => 'TestRepository1',
                'fullName' => 'TestUser1/TestRepository1',
                'url'      => 'https://github.com/TestUser1/TestRepository1',
                'owner'    => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
            ],
            22 => [
                'id'       => 22,
                'name'     => 'TestRepository2',
                'fullName' => 'TestUser1/TestRepository2',
                'url'      => 'https://github.com/TestUser1/TestRepository2',
                'owner'    => [
                    'id'        => 13,
                    'username'  => 'TestUser1',
                    'url'       => 'https://github.com/TestUser1',
                    'avatarUrl' => 'https://github.com/avatar1.png',
                ],
            ],
        ], $this->collection->toArray());
    }
}
