<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    public function setUp()
    {
        $this->response = $this->createMock(Response::class);
    }

    public function testNotFound()
    {
        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('NOT FOUND', $content);
            })
        ;

        $this->assertSame($this->response, (new Error($this->response))->notFound());
    }

    public function testNotAllowed()
    {
        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('METHOD NOT ALLOWED', $content);
            })
        ;

        $this->assertSame($this->response, (new Error($this->response))->methodNotAllowed());
    }
}
