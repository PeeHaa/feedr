<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

class AccessCode
{
    private $accessCode;

    public function __construct(string $accessCode)
    {
        $this->accessCode = $accessCode;
    }

    public function getCode(): string
    {
        return $this->accessCode;
    }
}
