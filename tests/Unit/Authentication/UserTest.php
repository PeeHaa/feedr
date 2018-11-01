<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User */
    private $user;

    public function setUp()
    {
        $this->user = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');
    }

    public function testGetId()
    {
        $this->assertSame(13, $this->user->getId());
    }

    public function testGetUsername()
    {
        $this->assertSame('TestUser1', $this->user->getUsername());
    }

    public function testGetUrl()
    {
        $this->assertSame('https://github.com/TestUser1', $this->user->getUrl());
    }

    public function testGetAvatarUrl()
    {
        $this->assertSame('https://github.com/avatar1.png', $this->user->getAvatarUrl());
    }

    public function testToArray()
    {
        $this->assertSame([
            'id'        => 13,
            'username'  => 'TestUser1',
            'url'       => 'https://github.com/TestUser1',
            'avatarUrl' => 'https://github.com/avatar1.png',
        ], $this->user->toArray());
    }
}
