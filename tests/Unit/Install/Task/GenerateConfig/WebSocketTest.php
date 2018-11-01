<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\WebSocket;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WebSocketTest extends TestCase
{
    /** @var MockObject|Output */
    private $output;

    private $configuration;

    /** @var MockObject|IOInterface */
    private $cliPrompt;

    public function setUp()
    {
        $this->output    = $this->createMock(Output::class);
        $this->cliPrompt = $this->createMock(IOInterface::class);

        $this->configuration = [];
    }

    public function testRunOutputsInfo()
    {
        $webSocket = new WebSocket();

        $this->output
            ->method('info')
            ->with('Verifying WebSocket configuration')
        ;

        $this->assertTrue(is_array($webSocket->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunCreatesBaseKey()
    {
        $webSocket = new WebSocket();

        $configuration = $webSocket->run($this->output, $this->configuration, $this->cliPrompt);

        $this->assertArrayHasKey('webSocket', $configuration);
    }

    public function testRunAsksForInternalHostname()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->expects($this->at(0))
            ->method('ask')
            ->willReturn('foo')
            ->with('What is the WebSocket internal hostname [127.0.0.1]: ')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->assertTrue(is_array($webSocket->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsDefaultInternalHostnameWhenNull()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn(null)
        ;

        $this->assertSame('127.0.0.1', $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['internalHostname']);
    }

    public function testRunSetsDefaultInternalHostnameWhenEmptyString()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('')
        ;

        $this->assertSame('127.0.0.1', $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['internalHostname']);
    }

    public function testRunAsksForInternalPort()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('ask')
            ->willReturn('foo')
            ->with('What is the WebSocket internal port [5000]: ')
        ;

        $this->assertTrue(is_array($webSocket->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsDefaultInternalPortWhenNull()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn(null)
        ;

        $this->assertSame(5000, $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['internalPort']);
    }

    public function testRunSetsDefaultInternalPortWhenEmptyString()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('')
        ;

        $this->assertSame(5000, $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['internalPort']);
    }

    public function testRunAsksForExpose()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('ask')
            ->willReturn('foo')
            ->with('Expose the internal address? This is to be used when not putting the websocket server behind a proxy [y]: ')
        ;

        $this->assertTrue(is_array($webSocket->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsDefaultExposeWhenNull()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn(null)
        ;

        $this->assertSame(true, $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['expose']);
    }

    public function testRunSetsDefaultExposeWhenEmptyString()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('')
        ;

        $this->assertSame(true, $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['expose']);
    }

    public function testRunSetsDefaultExposeWhenNotY()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('wat')
        ;

        $this->assertSame(false, $webSocket->run($this->output, $this->configuration, $this->cliPrompt)['webSocket']['expose']);
    }

    public function testRunOutputsSuccess()
    {
        $webSocket = new WebSocket();

        $this->output
            ->expects($this->once())
            ->method('success')
            ->with('WebSocket configuration generated')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturnOnConsecutiveCalls('foo', 'bar', 'baz')
        ;

        $this->assertTrue(is_array($webSocket->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsNewConfiguration()
    {
        $webSocket = new WebSocket();

        $this->cliPrompt
            ->method('ask')
            ->willReturnOnConsecutiveCalls('foo', 'bar', 'n')
        ;

        $this->assertSame([
            'webSocket' => [
                'internalHostname' => 'foo',
                'internalPort'     => 'bar',
                'expose'           => false,
            ],
        ], $webSocket->run($this->output, $this->configuration, $this->cliPrompt));
    }

    public function testRunMaintainsCurrentConfiguration()
    {
        $webSocket = new WebSocket();

        $configuration = [
            'webSocket' => [
                'internalHostname' => 'foo',
                'internalPort'     => 'bar',
                'expose'           => false,
            ],
        ];

        $this->assertSame($configuration, $webSocket->run($this->output, $configuration, $this->cliPrompt));
    }
}
