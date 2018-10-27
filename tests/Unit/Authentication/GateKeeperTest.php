<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Authentication;

use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GateKeeperTest extends TestCase
{
    /** @var MockObject|User */
    private $user;
    
    /** @var MockObject|GateKeeper */
    private $gateKeeper;

    public function setUp()
    {
        $this->user = new User(13, 'TestUser', 'https://github.com/TestUser', 'https://github.com/avatar/png');
        $this->gateKeeper = new GateKeeper;
    }

    public function testDefault()
    {
        $this->assertSame(false, $this->gateKeeper->isAuthorized());
    }

    public function testAuthorize()
    {
        $this->gateKeeper->authorize($this->user);
        $this->assertSame(true, $this->gateKeeper->isAuthorized());
    }

    public function testgetUserWithAuthorize()
    {
        $this->gateKeeper->authorize($this->user);
        $this->assertSame($this->user, $this->gateKeeper->getUser());
    }

    public function testDeAuthorize()
    {
        $this->gateKeeper->deAuthorize();
        $this->assertSame(false, $this->gateKeeper->isAuthorized());
    }
}
