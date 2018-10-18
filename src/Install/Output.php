<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install;

interface Output
{
    public function header(string $data): void;

    public function default(string $data): void;

    public function warning(string $data): void;

    public function error(string $data): void;

    public function info(string $data): void;

    public function success(string $data): void;
}
