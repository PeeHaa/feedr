<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;

class CompileAssets implements Task
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function run(Output $output): void
    {
        $output->header('Compiling resources');

        $this->installNodeModules($output);
        $this->compileResources($output);
    }

    private function installNodeModules(Output $output): void
    {
        $output->info('Installing node modules');

        $process = proc_open('npm install', [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, $this->basePath);

        $exitCode = proc_close($process);

        if ($exitCode !== -1) {
            $output->success('Node modules installed');

            return;
        }

        $output->error('Failed to install node modules');
    }

    private function compileResources(Output $output): void
    {
        $output->info('Compiling resources');

        $process = proc_open('npm run prod --quiet', [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, $this->basePath);

        $exitCode = proc_close($process);

        if ($exitCode !== -1) {
            $output->success('Resources compiled');

            return;
        }

        $output->error('Failed to compile resources');
    }
}
