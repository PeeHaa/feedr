<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateEncryptionKey;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenerateEncryptionKeyTest extends TestCase
{
    /** @var MockObject|Output */
    private $output;

    private $existingFile;

    private $newFile;

    public function setUp()
    {
        $this->existingFile = TEST_DATA_DIR . '/key-existing.key';
        $this->newFile      = TEST_DATA_DIR . '/key-new.key';
        $this->output       = $this->createMock(Output::class);
    }

    public function tearDown()
    {
        @unlink($this->newFile);
    }

    public function testRunOutputsHeader()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->existingFile);

        $this->output
            ->method('header')
            ->willReturnCallback(function($text) {
                $this->assertSame('Generate encryption key', $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }

    public function testRunOutputsCheckingForKeyInfo()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->existingFile);

        $this->output
            ->expects($this->at(0))
            ->method('info')
            ->willReturnCallback(function($text) {
                $this->assertSame('Checking for existing encryption key in: ' . $this->existingFile, $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }

    public function testRunOutputsSuccessWhenFileAlreadyExists()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->existingFile);

        $this->output
            ->expects($this->once())
            ->method('success')
            ->willReturnCallback(function($text) {
                $this->assertSame('Encryption key already exists', $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }

    public function testRunOutputsNoExistingKeyFoundInfo()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->newFile);

        $this->output
            ->expects($this->at(2))
            ->method('info')
            ->willReturnCallback(function($text) {
                $this->assertSame('No encryption key found. Generating now...', $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }

    public function testRunOutputsKeyGeneratedInfo()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->newFile);

        $this->output
            ->expects($this->at(3))
            ->method('info')
            ->willReturnCallback(function($text) {
                $this->assertSame('Key generated. Verifying key...', $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }

    public function testRunOutputsSuccessWhenKeyHasBeenGenerated()
    {
        $generateEncryptionKey = new GenerateEncryptionKey($this->newFile);

        $this->output
            ->expects($this->once())
            ->method('success')
            ->willReturnCallback(function($text) {
                $this->assertSame('Encryption key file successfully written to: ' . realpath($this->newFile), $text);
            })
        ;

        $generateEncryptionKey->run($this->output);
    }
}
