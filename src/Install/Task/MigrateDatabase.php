<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;

class MigrateDatabase implements Task
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function run(Output $output): void
    {
        $output->header('Migrating database');

        chdir($this->basePath . '/vendor/bin');

        shell_exec('phinx migrate -c ./../../phinx.yml -e production');

        $output->success('Database migrated');
    }
}
