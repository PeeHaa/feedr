<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task\GenerateConfig;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\ReloadRoutes;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReloadRoutesTest extends TestCase
{
    /** @var MockObject|Output */
    private $output;

    private $configuration;

    public function setUp()
    {
        $this->output = $this->createMock(Output::class);

        $this->configuration = [];
    }

    public function testRunOutputsInfo()
    {
        $reloadRoutes = new ReloadRoutes();

        $this->output
            ->method('info')
            ->with('Verifying router configuration')
        ;

        $this->assertTrue(is_array($reloadRoutes->run($this->output, $this->configuration)));
    }

    public function testRunWritesWarningWhenValueIsNotABoolean()
    {
        $reloadRoutes = new ReloadRoutes();

        $this->output
            ->expects($this->once())
            ->method('warning')
            ->with('Value for configuration key `reloadRoutes` is not valid')
        ;

        $reloadRoutes->run($this->output, ['reloadRoutes' => 'foo']);
    }

    public function testRunOutputsSuccess()
    {
        $reloadRoutes = new ReloadRoutes();

        $this->output
            ->expects($this->once())
            ->method('success')
            ->with('Router configuration generated')
        ;

        $this->assertTrue(is_array($reloadRoutes->run($this->output, $this->configuration)));
    }

    public function testRunSetsNewConfiguration()
    {
        $reloadRoutes = new ReloadRoutes();

        $this->assertSame([
            'reloadRoutes' => true,
        ], $reloadRoutes->run($this->output, $this->configuration));
    }

    public function testRunMaintainsCurrentConfiguration()
    {
        $reloadRoutes = new ReloadRoutes();

        $configuration = [
            'reloadRoutes' => false,
        ];

        $this->assertSame($configuration, $reloadRoutes->run($this->output, $configuration));
    }

    public function testRunSetsNewValueCurrentFaultyConfiguration()
    {
        $reloadRoutes = new ReloadRoutes();

        $configuration = [
            'reloadRoutes' => 'foo',
        ];

        $this->assertTrue($reloadRoutes->run($this->output, $configuration)['reloadRoutes']);
    }
}
