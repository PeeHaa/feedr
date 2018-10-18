<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

use Amp\Artax\Client;

class Releases
{
    private $credentials;

    private $httpClient;

    public function __construct(Credentials $credentials, Client $httpClient)
    {
        $this->credentials = $credentials;
        $this->httpClient  = $httpClient;
    }

    public function getRepositoryReleases(Repository $repository)
    {

    }
}
