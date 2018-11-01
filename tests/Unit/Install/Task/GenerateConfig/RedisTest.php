<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\Redis;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
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
        $redis = new Redis();

        $this->output
            ->method('info')
            ->with('Verifying redis configuration')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->assertTrue(is_array($redis->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunCreatesBaseKey()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $configuration = $redis->run($this->output, $this->configuration, $this->cliPrompt);

        $this->assertArrayHasKey('redis', $configuration);
    }

    public function testRunAsksForHost()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->at(0))
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('ask')
            ->willReturn('foo')
            ->with('What is the redis host [6379]: ')
        ;

        $this->assertTrue(is_array($redis->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsDefaultHostWhenNull()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->exactly(2))
            ->method('ask')
            ->willReturn(null)
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->assertSame('localhost', $redis->run($this->output, $this->configuration, $this->cliPrompt)['redis']['host']);
    }

    public function testRunSetsDefaultHostWhenEmptyString()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->exactly(2))
            ->method('ask')
            ->willReturn('')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->assertSame('localhost', $redis->run($this->output, $this->configuration, $this->cliPrompt)['redis']['host']);
    }

    public function testRunAsksForPort()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->at(0))
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('ask')
            ->willReturn('foo')
            ->with('What is the redis host [6379]: ')
        ;

        $this->assertTrue(is_array($redis->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsDefaultPortWhenNull()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->at(0))
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('ask')
            ->willReturn(null)
        ;

        $this->assertSame(6379, $redis->run($this->output, $this->configuration, $this->cliPrompt)['redis']['port']);
    }

    public function testRunSetsDefaultPortWhenEmptyString()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->expects($this->at(0))
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('ask')
            ->willReturn('')
        ;

        $this->assertSame(6379, $redis->run($this->output, $this->configuration, $this->cliPrompt)['redis']['port']);
    }

    public function testRunOutputsSuccess()
    {
        $redis = new Redis();

        $this->output
            ->expects($this->once())
            ->method('success')
            ->with('Redis configuration generated')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturnOnConsecutiveCalls('foo', 'bar')
        ;

        $this->assertTrue(is_array($redis->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsNewConfiguration()
    {
        $redis = new Redis();

        $this->cliPrompt
            ->method('ask')
            ->willReturnOnConsecutiveCalls('foo', 'bar')
        ;

        $this->assertSame([
            'redis' => [
                'host' => 'foo',
                'port' => 'bar',
            ],
        ], $redis->run($this->output, $this->configuration, $this->cliPrompt));
    }

    public function testRunMaintainsCurrentConfiguration()
    {
        $redis = new Redis();

        $configuration = [
            'redis' => [
                'host' => 'baz',
                'port' => 'qux',
            ],
        ];

        $this->assertSame($configuration, $redis->run($this->output, $configuration, $this->cliPrompt));
    }
}
