<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Output;

use PeeHaa\AwesomeFeed\Install\Output;

class StdOut implements Output
{
    private const WARNING_PREFIX = "\e[33m[ERROR]\e[0m";
    private const ERROR_PREFIX = "\e[31m[ERROR]\e[0m";
    private const INFO_PREFIX = "\e[1;34m[INFO]\e[0m";
    private const SUCCESS_PREFIX = "\e[32m[DONE]\e[0m";

    public function header(string $data): void
    {
        $this->default(PHP_EOL . $data);
    }

    public function default(string $data): void
    {
        $this->write(sprintf('%s', $data));
    }

    public function warning(string $data): void
    {
        $this->write(sprintf('%s %s', self::WARNING_PREFIX, $data));
    }

    public function error(string $data): void
    {
        $this->write(sprintf('%s %s', self::ERROR_PREFIX, $data));
    }

    public function info(string $data): void
    {
        $this->write(sprintf('%s %s', self::INFO_PREFIX, $data));
    }

    public function success(string $data): void
    {
        $this->write(sprintf('%s %s', self::SUCCESS_PREFIX, $data));
    }

    private function write(string $data): void
    {
        echo $data . PHP_EOL;
    }
}
