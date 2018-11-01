<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use PeeHaa\AwesomeFeed\GitHub\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /** @var Credentials */
    private $credentials;

    public function setUp()
    {
        $this->credentials = new Credentials('foo', 'bar');
    }

    public function testGetClientId()
    {
        $this->assertSame('foo', $this->credentials->getClientId());
    }

    public function testGetClientSecret()
    {
        $this->assertSame('bar', $this->credentials->getClientSecret());
    }
}
