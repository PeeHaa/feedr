<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install;

interface Task
{
    public function run(Output $output): void;
}
