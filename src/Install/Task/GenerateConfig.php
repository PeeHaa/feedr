<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\ReloadRoutes;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\GitHub;

class GenerateConfig implements Task
{
    private $configFile;

    private $cliPrompt;

    public function __construct(string $configFile, IOInterface $cliPrompt)
    {
        $this->configFile = $configFile;
        $this->cliPrompt  = $cliPrompt;
    }

    public function run(Output $output): void
    {
        $output->header('Generating configuration');

        $output->info('Fetching current configuration');

        $configuration = $this->getCurrentConfiguration();

        $configuration = (new ReloadRoutes())->run($output, $configuration);
        $configuration = (new GitHub())->run($output, $configuration, $this->cliPrompt);

        $output->info('Writing configuration file');

        $this->writeToFile($configuration);

        $output->success('Configuration written to ' . realpath($this->configFile));
    }

    private function getCurrentConfiguration(): array
    {
        if (file_exists($this->configFile)) {
            /** @noinspection PhpIncludeInspection */
            return require $this->configFile;
        }

        return [];
    }

    private function writeToFile(array $configuration): void
    {
        file_put_contents($this->configFile, sprintf(
            '<?php declare(strict_types=1);%sreturn %s;%s',
            PHP_EOL . PHP_EOL,
            var_export($configuration, true),
            PHP_EOL
        ));
    }
}
