<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\GitHub;

use Amp\Artax\Client;
use Amp\Artax\FormBody;
use Amp\Artax\Request;
use Amp\Artax\Response;
use CodeCollab\Http\Session\Session;
use function Amp\call;
use function Amp\Promise\wait;

class Authorization
{
    private $credentials;

    private $session;

    private $httpClient;

    public function __construct(Credentials $credentials, Session $session, Client $httpClient)
    {
        $this->credentials = $credentials;
        $this->session     = $session;
        $this->httpClient  = $httpClient;
    }

    public function getUrl(string $redirectUri): string
    {
        return 'https://github.com/login/oauth/authorize' . $this->getUrlQueryString($redirectUri);
    }

    private function getUrlQueryString(string $redirectUri): string
    {
        $nonce = bin2hex(random_bytes(16));

        $this->session->set('gitHubNonce', $nonce);

        return '?' .http_build_query([
            'client_id'    => $this->credentials->getClientId(),
            'redirect_uri' => $redirectUri,
            'state'        => $nonce,
        ]);
    }

    public function isStateValid(string $state): bool
    {
        if (!$this->session->exists('gitHubNonce')) {
            return false;
        }

        return hash_equals($state, $this->session->get('gitHubNonce'));
    }

    public function getAccessToken(string $code): array
    {
        $requestBody = new FormBody();
        $requestBody->addField('client_id', $this->credentials->getClientId());
        $requestBody->addField('client_secret', $this->credentials->getClientSecret());
        $requestBody->addField('code', $code);
        $requestBody->addField('state', $this->session->get('gitHubNonce'));

        $request = (new Request('https://github.com/login/oauth/access_token', 'POST'))
            ->withBody($requestBody)
            ->withHeader('Accept', 'application/json')
        ;

        return wait(call(function() use ($request) {
            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            return json_decode($body, true);
        }));
    }

    public function getUserInformation(string $accessCode): array
    {
        return wait(call(function() use ($accessCode) {
            $request = (new Request(ApiRequestInformation::BASE_URL . '/user'))
                ->withHeader('Accept', ApiRequestInformation::VERSION_HEADER)
                ->withHeader('Authorization', 'token ' . $accessCode)
            ;

            /** @var Response $response */
            $response = yield $this->httpClient->request($request);
            $body     = yield $response->getBody();

            return json_decode($body, true);
        }));
    }
}
