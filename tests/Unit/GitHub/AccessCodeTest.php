<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use PeeHaa\AwesomeFeed\GitHub\AccessCode;
use PHPUnit\Framework\TestCase;

class AccessCodeTest extends TestCase
{
    public function testGetCode()
    {
        $accessCode = new AccessCode('foobar');

        $this->assertSame('foobar', $accessCode->getCode());
    }
}
