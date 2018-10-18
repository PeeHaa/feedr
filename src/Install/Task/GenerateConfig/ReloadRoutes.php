<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task\GenerateConfig;

use PeeHaa\AwesomeFeed\Install\Output;

class ReloadRoutes
{
    public function run(Output $output, array $configuration): array
    {
        $output->info('Verifying router configuration');

        if (!$this->isValid($configuration)) {
            $output->warning('Value for configuration key `reloadRoutes` is not valid');
        }

        if (!array_key_exists('reloadRoutes', $configuration) || !$this->isValid($configuration)) {
            $configuration['reloadRoutes'] = true;

            $output->success('Router configuration generated');
        }

        return $configuration;
    }

    private function isValid(array $configuration): bool
    {
        if (!array_key_exists('reloadRoutes', $configuration)) {
            return true;
        }

        return is_bool($configuration['reloadRoutes']);
    }
}
