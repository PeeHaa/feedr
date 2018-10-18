<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;

class GitHub
{
    public function run(Output $output, array $configuration, IOInterface $cliPrompt): array
    {
        $output->info('Verifying GitHub configuration');

        if (!array_key_exists('gitHub', $configuration)) {
            $configuration['gitHub'] = [];
        }

        if (!array_key_exists('clientId', $configuration['gitHub'])) {
            do {
                $configuration['gitHub']['clientId'] = $cliPrompt->ask(
                    'What is the GitHub client id (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): '
                );
            } while(!$configuration['gitHub']['clientId']);
        }

        if (!array_key_exists('clientSecret', $configuration['gitHub'])) {
            do {
                $configuration['gitHub']['clientSecret'] = $cliPrompt->askAndHideAnswer(
                    'What is the GitHub client secret (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): '
                );
            } while(!$configuration['gitHub']['clientSecret']);
        }

        $output->success('GitHub configuration generated');

        return $configuration;
    }
}
