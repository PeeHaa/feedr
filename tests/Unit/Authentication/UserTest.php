<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var MockObject|User */
    private $user;

    public function setUp()
    {
        $this->user = new User(13, 'TestUser', 'https://github.com/TestUser', 'https://github.com/avatar/png');
    }

    public function testGetId()
    {
        $this->assertSame(13, $this->user->getId());
    }

    public function testGetUsername()
    {
        $this->assertSame('TestUser', $this->user->getUsername());
    }

    public function testGetUrl()
    {
        $this->assertSame('https://github.com/TestUser', $this->user->getUrl());
    }

    public function testGetAvatarUrl()
    {
        $this->assertSame('https://github.com/avatar/png', $this->user->getAvatarUrl());
    }

    public function testToArray()
    {
        $this->assertSame([
            'id'        => 13,
            'username'  => 'TestUser',
            'url'       => 'https://github.com/TestUser',
            'avatarUrl' => 'https://github.com/avatar/png',
        ], $this->user->toArray());
    }
}
