<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub\Release;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection;
use PeeHaa\AwesomeFeed\GitHub\Release\Release;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /** @var Release */
    private $release1;
    
    /** @var Release */
    private $release2;
    
    /** @var Release */
    private $release3;
    
    /** @var Collection */
    private $collection;

    public function setUp()
    {
        $createdBy = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');

        $repository = new Repository(
            21,
            'TestRepository',
            'TestUser1/TestRepository',
            'https://github.com/TestUser1/TestRepository',
            $createdBy
        );

        $this->release1 = new Release(
            20,
            'v0.5.5',
            'Release body',
            'https://github.com/release-url',
            $repository,
            new \DateTimeImmutable('@946684800')
        );

        $this->release2 = new Release(
            21,
            'v0.5.6',
            'Release body',
            'https://github.com/release-url',
            $repository,
            new \DateTimeImmutable('@946684800')
        );

        $this->release3 = new Release(
            22,
            'v0.5.7',
            'Release body',
            'https://github.com/release-url',
            $repository,
            new \DateTimeImmutable('@946684800')
        );

        $this->collection = new Collection;
    }

    public function addRemovesDuplicates()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release1);

        $this->assertSame(2, $this->collection->count());
    }

    public function testCurrent()
    {
        $this->collection->add($this->release1);

        $this->assertSame($this->release1, $this->collection->current());
    }

    public function testNext()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $this->collection->next();

        $this->assertSame($this->release2, $this->collection->current());
    }

    public function testKey()
    {
        $this->collection->add($this->release1);

        $this->assertSame(20, $this->collection->key());
    }

    public function testValidWithUser()
    {
        $this->collection->add($this->release1);

        $this->assertTrue($this->collection->valid());
    }

    public function testValidWithoutUser()
    {
        $this->assertFalse($this->collection->valid());
    }

    public function testValidWhenFinishedIterating()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $this->collection->next();
        $this->collection->next();
        $this->collection->next();

        $this->assertFalse($this->collection->valid());
    }

    public function testRewind()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $this->collection->next();

        $this->collection->rewind();

        $this->collection->next();

        $this->assertSame($this->release2, $this->collection->current());
    }

    public function testCount()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $this->assertSame(3, $this->collection->count());
    }

    public function testContainsWithUser()
    {
        $this->collection->add($this->release1);

        $this->assertTrue($this->collection->contains($this->release1));
    }

    public function testContainsWithoutUser()
    {
        $this->assertFalse($this->collection->contains($this->release1));
    }

    public function testContainsWithUserWithMatch()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);

        $this->assertTrue($this->collection->contains($this->release2));
    }

    public function testFilterIsImmutable()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $filteredCollection = $this->collection->filter(function(Release $release) {
            return $release->getId() === 21;
        });

        $this->assertNotSame($filteredCollection->key(), $this->collection);
    }

    public function testFilterFilters()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);
        $this->collection->add($this->release3);

        $filteredCollection = $this->collection->filter(function(Release $release) {
            return $release->getId() === 21;
        });
        
        $this->assertSame(1, $filteredCollection->count());

        $this->assertFalse($filteredCollection->contains($this->release1));
        $this->assertFalse($filteredCollection->contains($this->release3));

        $this->assertTrue($filteredCollection->contains($this->release2));
    }

    public function testToArray()
    {
        $this->collection->add($this->release1);

        $this->assertSame([
            20 => [
                'id'            => 20,
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
                'name'          => 'v0.5.5',
                'body'          => 'Release body',
                'url'           => 'https://github.com/release-url',
                'publishedDate' => '946684800',
            ],
        ], $this->collection->toArray());
    }

    public function testToArrayWithMultipleUsers()
    {
        $this->collection->add($this->release1);
        $this->collection->add($this->release2);

        $this->assertSame([
            20 => [
                'id'            => 20,
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
                'name'          => 'v0.5.5',
                'body'          => 'Release body',
                'url'           => 'https://github.com/release-url',
                'publishedDate' => '946684800',
            ],
            21 => [
                'id'            => 21,
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
                'name'          => 'v0.5.6',
                'body'          => 'Release body',
                'url'           => 'https://github.com/release-url',
                'publishedDate' => '946684800',
            ],
        ], $this->collection->toArray());
    }
}
