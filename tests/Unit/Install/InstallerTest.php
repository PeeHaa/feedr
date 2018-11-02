<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install;

use PeeHaa\AwesomeFeed\Install\Installer;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    /** @var Installer */
    private $installer;

    /** @var MockObject|Output */
    private $output;

    public function setUp()
    {
        $this->output    = $this->createMock(Output::class);
        $this->installer = new Installer($this->output);
    }

    public function testRunsAddedTask()
    {
        /** @var MockObject|Task $task */
        $task = $this->createMock(Task::class);

        $task
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function($test) {
                $this->assertSame($test, $this->output);
            })
        ;

        $this->installer->addTask($task);

        $this->installer->run();
    }

    public function testRunsAddedMultipleTasks()
    {
        /** @var MockObject|Task $task */
        $task1 = $this->createMock(Task::class);
        /** @var MockObject|Task $task */
        $task2 = $this->createMock(Task::class);

        $task1
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function($test) {
                $this->assertSame($test, $this->output);
            })
        ;

        $task2
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function($test) {
                $this->assertSame($test, $this->output);
            })
        ;

        $this->installer->addTask($task1);
        $this->installer->addTask($task2);

        $this->installer->run();
    }
}
