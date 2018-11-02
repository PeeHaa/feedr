<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task\GenerateDatabaseConfig;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateDatabaseConfig\Phinx;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhinxTest extends TestCase
{
    /** @var MockObject|Output */
    private $output;

    private $configuration;

    private $existingFile;

    private $newFile;

    public function setUp()
    {
        $this->output = $this->createMock(Output::class);

        $this->configuration = [
            'database' => [
                'host'     => 'testhost',
                'name'     => 'testname',
                'username' => 'testuser',
                'password' => 'testpass',
            ],
        ];

        $this->existingFile = TEST_DATA_DIR . '/phinx-existing.yml';
        $this->newFile      = TEST_DATA_DIR . '/phinx-new.yml';
    }

    public function tearDown()
    {
        parent::tearDown();

        @unlink($this->newFile);
    }

    public function testRunOutputsInfo()
    {
        $phinx = new Phinx();

        $this->output
            ->method('info')
            ->willReturnCallback(function($message) {
                $this->assertSame('Writing phinx configuration file', $message);
            })
        ;

        $phinx->run($this->output, $this->configuration, $this->existingFile);
    }

    public function testRunOutputSuccessMessageOnExistingConfig()
    {
        $phinx = new Phinx();

        $this->output
            ->method('success')
            ->willReturnCallback(function($message) {
                $this->assertSame('Phinx configuration already exists at: ' . realpath($this->existingFile), $message);
            })
        ;

        $phinx->run($this->output, $this->configuration, $this->existingFile);
    }

    public function testRunGeneratesConfigFile()
    {
        $phinx = new Phinx();

        $phinx->run($this->output, $this->configuration, $this->newFile);

        $this->assertTrue(file_exists($this->newFile));
    }

    public function testRunGeneratesConfigFileContents()
    {
        $phinx = new Phinx();

        $phinx->run($this->output, $this->configuration, $this->newFile);

        $config = file_get_contents($this->newFile);

        $this->assertSame(1, preg_match('~host: testhost~m', $config));
        $this->assertSame(1, preg_match('~name: testname~m', $config));
        $this->assertSame(1, preg_match('~user: testuser~m', $config));
        $this->assertSame(1, preg_match('~pass: \'testpass\'~m', $config));
    }

    public function testRunOutputsSuccess()
    {
        $this->output
            ->method('success')
            ->willReturnCallback(function($message) {
                $this->assertSame('Phinx configuration written to: ' . realpath($this->newFile), $message);
            })
        ;

        $phinx = new Phinx();

        $phinx->run($this->output, $this->configuration, $this->newFile);
    }
}
