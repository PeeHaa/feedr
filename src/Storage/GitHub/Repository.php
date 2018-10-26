<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\GitHub;

use Amp\Artax\Client;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Promise;
use Amp\Success;
use PeeHaa\AwesomeFeed\Authentication\User as UserEntity;
use PeeHaa\AwesomeFeed\GitHub\AccessToken;
use PeeHaa\AwesomeFeed\GitHub\ApiRequestInformation;
use PeeHaa\AwesomeFeed\GitHub\Collection;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection as ReleaseCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Release;
use PeeHaa\AwesomeFeed\GitHub\Repository as RepositoryEntity;
use function Amp\call;
use function Amp\Promise\wait;

class Repository
{
    private $accessToken;

    private $httpClient;

    public function __construct(AccessToken $accessToken, Client $httpClient)
    {
        $this->accessToken = $accessToken;
        $this->httpClient  = $httpClient;
    }

    public function search(string $query): Collection
    {
        if (preg_match('~^https://github.com/([^/]+)/([^/]+)~', $query, $matches) === 1) {
            return $this->getRepositoriesByIdentifiers(sprintf('%s/%s', $matches[1], $matches[2]));
        }

        if (preg_match('~^([^/]+)/([^/]+)$~', $query, $matches) === 1) {
            return $this->getRepositoriesByIdentifiers(sprintf('%s/%s', $matches[1], $matches[2]));
        }

        return $this->getRepositoriesBySearch($query);
    }

    public function getRepositoriesByIdentifiers(string ...$identifiers): Collection
    {
        return wait(call(function() use ($identifiers) {
            $collection = new Collection();

            $promises = [];

            foreach ($identifiers as $identifier) {
                $promises[] = $this->getRepositoryByIdentifier($identifier);
            }

            $repositories = yield $promises;

            foreach ($repositories as $repository) {
                if ($repository === null) {
                    continue;
                }

                $collection->add($repository);
            }

            return $collection;
        }));
    }

    private function getRepositoryByIdentifier(string $identifier): Promise
    {
        return call(function() use ($identifier) {
            $ownerAndName = explode('/', $identifier);

            $identifier = sprintf('%s/%s', rawurlencode($ownerAndName[0]), rawurlencode($ownerAndName[1]));

            $request = (new Request(ApiRequestInformation::BASE_URL . '/repos/' . $identifier))
                ->withHeader('Accept', ApiRequestInformation::VERSION_HEADER)
                ->withHeader('Authorization', 'token ' . $this->accessToken->getToken())
            ;

            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            if ($response->getStatus() !== 200) {
                return null;
            }

            $repository = json_decode($body, true);

            return new RepositoryEntity(
                $repository['id'],
                $repository['name'],
                $repository['full_name'],
                $repository['html_url'],
                new UserEntity(
                    $repository['owner']['id'],
                    $repository['owner']['login'],
                    $repository['owner']['html_url'],
                    $repository['owner']['avatar_url']
                )
            );
        });
    }

    private function getRepositoriesBySearch(string $query): Collection
    {
        return wait(call(function() use ($query) {
            $request = (new Request(ApiRequestInformation::BASE_URL . '/search/repositories?q=' . rawurlencode($query)))
                ->withHeader('Accept', ApiRequestInformation::VERSION_HEADER)
                ->withHeader('Authorization', 'token ' . $this->accessToken->getToken())
            ;

            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            $collection = new Collection();

            if ($response->getStatus() !== 200) {
                return $collection;
            }

            $repositories = json_decode($body, true);

            foreach ($repositories['items'] as $repository) {
                $collection->add(new RepositoryEntity(
                    $repository['id'],
                    $repository['name'],
                    $repository['full_name'],
                    $repository['html_url'],
                    new UserEntity(
                        $repository['owner']['id'],
                        $repository['owner']['login'],
                        $repository['owner']['html_url'],
                        $repository['owner']['avatar_url']
                    )
                ));
            }

            return $collection;
        }));
    }

    public function getReleasesForRepository(RepositoryEntity $repository): Promise
    {
        return call(function() use ($repository) {
            $request = (new Request(ApiRequestInformation::BASE_URL . '/repos/' . $repository->getFullName() . '/releases'))
                ->withHeader('Accept', ApiRequestInformation::VERSION_HEADER)
                ->withHeader('Authorization', 'token ' . $this->accessToken->getToken())
            ;

            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            $collection = new ReleaseCollection();

            $releases = json_decode($body, true);

            foreach ($releases as $release) {
                $collection->add(new Release(
                    $release['id'],
                    $repository->getId(),
                    $release['name'],
                    $release['body'],
                    $release['html_url'],
                    new \DateTimeImmutable($release['published_at'])
                ));
            }

            return $collection;
        });
    }
}
