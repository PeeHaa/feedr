<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PHPUnit\Framework\TestCase;

class GateKeeperTest extends TestCase
{
    /** @var User */
    private $user1;

    /** @var User */
    private $user2;
    
    /** @var GateKeeper */
    private $gateKeeper;

    public function setUp()
    {
        $this->user1 = new User(13, 'TestUser1', 'https://github.com/TestUser1', 'https://github.com/avatar1.png');
        $this->user2 = new User(14, 'TestUser2', 'https://github.com/TestUser2', 'https://github.com/avatar2.png');

        $this->gateKeeper = new GateKeeper;
    }

    public function testUnauthorizedGuest()
    {
        $this->assertFalse($this->gateKeeper->isAuthorized());
    }

    public function testAuthorizedUser()
    {
        $this->gateKeeper->authorize($this->user1);

        $this->assertTrue($this->gateKeeper->isAuthorized());
    }

    public function testGetUserWhenNotAuthorized()
    {
        $this->assertNull($this->gateKeeper->getUser());
    }

    public function testGetUserWhenAuthorized()
    {
        $this->gateKeeper->authorize($this->user1);

        $this->assertSame($this->user1, $this->gateKeeper->getUser());
    }

    public function testAuthorizeOverwritesPreviousUser()
    {
        $this->gateKeeper->authorize($this->user1);

        $this->assertSame($this->user1, $this->gateKeeper->getUser());

        $this->gateKeeper->authorize($this->user2);

        $this->assertSame($this->user2, $this->gateKeeper->getUser());

        $this->assertTrue($this->gateKeeper->isAuthorized());
    }

    public function testDeAuthorize()
    {
        $this->gateKeeper->authorize($this->user1);

        $this->assertTrue($this->gateKeeper->isAuthorized());

        $this->gateKeeper->deAuthorize();

        $this->assertFalse($this->gateKeeper->isAuthorized());
    }
}
