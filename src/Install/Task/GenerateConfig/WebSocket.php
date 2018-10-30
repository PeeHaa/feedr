<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;

class WebSocket
{
    public function run(Output $output, array $configuration, IOInterface $cliPrompt): array
    {
        $output->info('Verifying WebSocket configuration');

        if (!array_key_exists('webSocket', $configuration)) {
            $configuration['webSocket'] = [];
        }

        if (!array_key_exists('internalHostname', $configuration['webSocket'])) {
            $configuration['webSocket']['internalHostname'] = $cliPrompt->ask(
                'What is the WebSocket internal hostname [127.0.0.1]: '
            );

            if ($configuration['webSocket']['internalHostname'] === null || trim($configuration['webSocket']['internalHostname']) === '') {
                $configuration['webSocket']['internalHostname'] = '127.0.0.1';
            }
        }

        if (!array_key_exists('internalPort', $configuration['webSocket'])) {
            $configuration['webSocket']['internalPort'] = $cliPrompt->ask(
                'What is the WebSocket internal port [5000]: '
            );

            if ($configuration['webSocket']['internalPort'] === null || trim($configuration['webSocket']['internalPort']) === '') {
                $configuration['webSocket']['internalPort'] = 5000;
            }
        }

        if (!array_key_exists('expose', $configuration['webSocket'])) {
            $configuration['webSocket']['expose'] = $cliPrompt->ask(
                'Expose the internal address? This is to be used when not putting the websocket server behind a proxy [y]: '
            );

            if ($configuration['webSocket']['expose'] === null || trim($configuration['webSocket']['expose']) === '' || trim($configuration['webSocket']['expose']) === 'y') {
                $configuration['webSocket']['expose'] = true;
            } else {
                $configuration['webSocket']['expose'] = false;
            }
        }

        $output->success('WebSocket configuration generated');

        return $configuration;
    }
}
