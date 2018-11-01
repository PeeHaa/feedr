<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Output;

use PeeHaa\AwesomeFeed\Install\Output\StdOut;
use PHPUnit\Framework\TestCase;

class StdOutTest extends TestCase
{
    /** @var StdOut */
    private $stdOut;

    public function setUp()
    {
        $this->stdOut = new StdOut();
    }

    public function testHeader()
    {
        $this->expectOutputString(PHP_EOL . 'foo' . PHP_EOL);

        $this->stdOut->header('foo');
    }

    public function testDefault()
    {
        $this->expectOutputString('foo' . PHP_EOL);

        $this->stdOut->default('foo');
    }

    public function testWarning()
    {
        $this->expectOutputString("\e[43;37;1m[WARN]\e[0m " . 'foo' . PHP_EOL);

        $this->stdOut->warning('foo');
    }

    public function testError()
    {
        $this->expectOutputString("\e[41;37;1m[ERROR]\e[0m " . 'foo' . PHP_EOL);

        $this->stdOut->error('foo');
    }

    public function testInfo()
    {
        $this->expectOutputString("\e[44;37;1m[INFO]\e[0m " . 'foo' . PHP_EOL);

        $this->stdOut->info('foo');
    }

    public function testSuccess()
    {
        $this->expectOutputString("\e[42;37;1m[DONE]\e[0m " . 'foo' . PHP_EOL);

        $this->stdOut->success('foo');
    }
}
