<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

class AccessToken
{
    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getToken(): string
    {
        return $this->accessToken;
    }
}
