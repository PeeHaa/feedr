<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;

class GenerateDatabaseConfig implements Task
{
    private $configFile;

    private $phinxConfigFile;

    private $cliPrompt;

    public function __construct(string $configFile, string $phinxConfigFile, IOInterface $cliPrompt)
    {
        $this->configFile      = $configFile;
        $this->phinxConfigFile = $phinxConfigFile;
        $this->cliPrompt       = $cliPrompt;
    }

    public function run(Output $output): void
    {
        $output->header('Generating database configuration');

        $output->info('Fetching current configuration');

        $configuration = $this->getCurrentConfiguration();

        if (!array_key_exists('database', $configuration)) {
            $configuration['database'] = [];
        }

        if (!array_key_exists('host', $configuration['database'])) {
            do {
                $configuration['database']['host'] = $this->cliPrompt->ask(
                    'What is the database host: '
                );
            } while (!$configuration['database']['host']);
        }

        if (!array_key_exists('name', $configuration['database'])) {
            do {
                $configuration['database']['name'] = $this->cliPrompt->ask(
                    'What is the database name (make sure the database is created): '
                );
            } while (!$configuration['database']['name']);
        }

        if (!array_key_exists('username', $configuration['database'])) {
            do {
                $configuration['database']['username'] = $this->cliPrompt->ask(
                    'What is the database username: '
                );
            } while (!$configuration['database']['username']);
        }

        if (!array_key_exists('password', $configuration['database'])) {
            do {
                $configuration['database']['password'] = $this->cliPrompt->askAndHideAnswer(
                    'What is the database password: '
                );
            } while (!$configuration['database']['password']);
        }

        $output->info('Writing configuration file');

        $this->writeToFile($configuration);

        $output->success('Configuration written to: ' . realpath($this->configFile));

        (new Task\GenerateDatabaseConfig\Phinx())->run($output, $configuration, $this->phinxConfigFile);
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
