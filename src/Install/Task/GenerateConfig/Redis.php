<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;

class Redis
{
    public function run(Output $output, array $configuration, IOInterface $cliPrompt): array
    {
        $output->info('Verifying redis configuration');

        if (!array_key_exists('redis', $configuration)) {
            $configuration['redis'] = [];
        }

        if (!array_key_exists('host', $configuration['redis'])) {
            $configuration['redis']['host'] = $cliPrompt->ask(
                'What is the redis host [localhost]: '
            );

            if ($configuration['redis']['host'] === null || trim($configuration['redis']['host']) === '') {
                $configuration['redis']['host'] = 'localhost';
            }
        }

        if (!array_key_exists('port', $configuration['redis'])) {
            $configuration['redis']['port'] = $cliPrompt->ask(
                'What is the redis host [6379]: '
            );

            if ($configuration['redis']['port'] === null || trim($configuration['redis']['port']) === '') {
                $configuration['redis']['port'] = 6379;
            }
        }

        $output->success('Redis configuration generated');

        return $configuration;
    }
}
