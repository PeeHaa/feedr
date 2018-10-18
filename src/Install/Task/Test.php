<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task;

use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task;

class Test implements Task
{
    public function run(Output $output): void
    {
        $output->header('This is a test command.');
        $output->default('This is the default output.');
        $output->warning('Just a warning...');
        $output->error('Something went wrong!');
        $output->info('This is just informational.');
        $output->success('Great success! \o/');
    }
}
