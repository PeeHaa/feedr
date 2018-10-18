<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use CodeCollab\Encryption\Defusev2\Key;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;

class GenerateEncryptionKey implements Task
{
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function run(Output $output): void
    {
        $output->header('Generate encryption key');

        $output->info('Checking for existing encryption key in: ' . $this->filename);

        if ($this->encryptionKeyExists()) {
            $output->success('Encryption key already exists');

            return;
        }

        $output->info('No encryption key found. Generating now...');

        $this->generateKey();

        $output->info('Key generated. Verifying key...');

        if (!$this->encryptionKeyExists()) {
            $output->error(sprintf('Encryption key file `%s` could not be written', $this->filename));

            return;
        }

        $output->success('Encryption key file successfully written to: ' . realpath($this->filename));
    }

    private function encryptionKeyExists(): bool
    {
        clearstatcache();

        return is_file($this->filename);
    }

    private function generateKey(): void
    {
        file_put_contents($this->filename, (new Key())->generate());
    }
}
