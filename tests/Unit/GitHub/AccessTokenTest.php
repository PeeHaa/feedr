<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use PeeHaa\AwesomeFeed\GitHub\AccessToken;
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    public function testGetToken()
    {
        $accessToken = new AccessToken('foobar');

        $this->assertSame('foobar', $accessToken->getToken());
    }
}
