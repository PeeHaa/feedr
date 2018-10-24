<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\GitHub;

use Amp\Artax\Client;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Promise;
use PeeHaa\AwesomeFeed\Authentication\Collection;
use PeeHaa\AwesomeFeed\Authentication\User as UserEntity;
use PeeHaa\AwesomeFeed\GitHub\AccessToken;
use PeeHaa\AwesomeFeed\GitHub\ApiRequestInformation;
use function Amp\call;
use function Amp\Promise\wait;

class User
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
        if (preg_match('~^https://github.com/([^/]+)~', $query, $matches) === 1) {
            return $this->getUsersByUsernames($matches[1]);
        }

        return $this->getUsersBySearch($query);
    }

    public function getUsersByUsernames(string ...$usernames): Collection
    {
        return wait(call(function() use ($usernames) {
            $collection = new Collection();

            $promises = [];

            foreach ($usernames as $username) {
                $promises[] = $this->getUserByUsername($username);
            }

            $users = yield $promises;

            foreach ($users as $user) {
                if ($user === null) {
                    continue;
                }

                $collection->add($user);
            }

            return $collection;
        }));
    }

    private function getUserByUsername(string $username): Promise
    {
        return call(function() use ($username) {
            $request = (new Request(ApiRequestInformation::BASE_URL . '/users/' . rawurlencode($username)))
                ->withHeader('Accept', ApiRequestInformation::VERSION_HEADER)
                ->withHeader('Authorization', 'token ' . $this->accessToken->getToken())
            ;

            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            if ($response->getStatus() !== 200) {
                return null;
            }

            $user = json_decode($body, true);

            return new UserEntity(
                $user['id'],
                $user['login'],
                $user['html_url'],
                $user['avatar_url']
            );
        });
    }

    private function getUsersBySearch(string $query): Collection
    {
        return wait(call(function() use ($query) {
            $request = (new Request(ApiRequestInformation::BASE_URL . '/search/users?q=' . rawurlencode($query) . '+type:user'))
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

            $users = json_decode($body, true);

            foreach ($users['items'] as $user) {
                $collection->add(new UserEntity(
                    $user['id'],
                    $user['login'],
                    $user['html_url'],
                    $user['avatar_url']
                ));
            }

            return $collection;
        }));
    }
}
