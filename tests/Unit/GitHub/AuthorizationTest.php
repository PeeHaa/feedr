<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\GitHub;

use Amp\Artax\Client;
use Amp\Artax\Response;
use Amp\ByteStream\InputStream;
use Amp\ByteStream\Message;
use Amp\Success;
use CodeCollab\Http\Session\Session;
use PeeHaa\AwesomeFeed\GitHub\Authorization;
use PeeHaa\AwesomeFeed\GitHub\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthorizationTest extends TestCase
{
    /** @var Credentials */
    private $credentials;

    /** @var MockObject|Session */
    private $session;

    /** @var MockObject|Client */
    private $httpClient;

    public function setUp()
    {
        $this->credentials = new Credentials('foo', 'bar');
        $this->session     = $this->createMock(Session::class);
        $this->httpClient  = $this->createMock(Client::class);
    }

    public function testGetUrl()
    {
        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $pattern = '~^https://github.com/login/oauth/authorize\?client_id=foo&redirect_uri=https%3A%2F%2Ffeedr.pieterhordijk.com&state=[a-f0-9]{32}$~';

        $this->assertRegExp($pattern, $authorization->getUrl('https://feedr.pieterhordijk.com'));
    }

    public function testIsStateValidReturnsFalseOnMissingState()
    {
        $this->session
            ->method('exists')
            ->willReturn(false)
        ;

        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $this->assertFalse($authorization->isStateValid('bar'));
    }

    public function testIsStateValidReturnsFalseOnNonMatchingState()
    {
        $this->session
            ->method('exists')
            ->willReturn(true)
        ;

        $this->session
            ->method('get')
            ->willReturn('foo')
        ;

        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $this->assertFalse($authorization->isStateValid('bar'));
    }

    public function testIsStateValidReturnsTrueWhenValid()
    {
        $this->session
            ->method('exists')
            ->willReturn(true)
        ;

        $this->session
            ->method('get')
            ->willReturn('foo')
        ;

        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $this->assertTrue($authorization->isStateValid('foo'));
    }

    public function testGetAccessCode()
    {
        $this->session
            ->method('get')
            ->willReturn('foo')
        ;

        $result = [
            'result' => 'ok',
        ];

        /** @var MockObject|InputStream $stream */
        $stream = $this->createMock(InputStream::class);

        $stream
            ->method('read')
            ->willReturnOnConsecutiveCalls(new Success(
                json_encode($result)),
                new Success(null)
            )
        ;

        $message = new Message($stream);

        $response = $this->createMock(Response::class);

        $response
            ->method('getBody')
            ->willReturn($message)
        ;

        $this->httpClient
            ->method('request')
            ->willReturn(new Success($response))
        ;

        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $this->assertSame($result, $authorization->getAccessToken('code'));
    }

    public function testGetUserInformation()
    {
        $this->session
            ->method('get')
            ->willReturn('foo')
        ;

        $result = [
            'result' => 'ok',
        ];

        /** @var MockObject|InputStream $stream */
        $stream = $this->createMock(InputStream::class);

        $stream
            ->method('read')
            ->willReturnOnConsecutiveCalls(new Success(
                json_encode($result)),
                new Success(null)
            )
        ;

        $message = new Message($stream);

        $response = $this->createMock(Response::class);

        $response
            ->method('getBody')
            ->willReturn($message)
        ;

        $this->httpClient
            ->method('request')
            ->willReturn(new Success($response))
        ;

        $authorization = new Authorization($this->credentials, $this->session, $this->httpClient);

        $this->assertSame($result, $authorization->getUserInformation('code'));
    }
}
