<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install;

class Installer
{
    private $output;

    /** @var Task[] */
    private $tasks = [];

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    public function addTask(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function run(): void
    {
        foreach ($this->tasks as $task) {
            $task->run($this->output);
        }
    }
}
