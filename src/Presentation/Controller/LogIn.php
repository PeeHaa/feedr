<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use CodeCollab\Http\Session\Session;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Form\Authentication\GitHubLogIn;
use PeeHaa\AwesomeFeed\GitHub\Authorization;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;

class LogIn
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(Html $template, GitHubLogIn $gitHubLogIn): Response
    {
        $this->response->setContent($template->renderPage('/authentication/login.phtml', [
            'gitHubForm' => $gitHubLogIn,
        ]));

        return $this->response;
    }

    public function processGitHubLogIn(
        Html $template,
        GitHubLogIn $gitHubLogIn,
        Request $request,
        Authorization $authorization,
        UrlBuilder $urlBuilder
    ): Response {
        $gitHubLogIn->bindRequest($request);

        if (!$gitHubLogIn->isValid()) {
            return $this->render($template, $gitHubLogIn);
        }

        $this->response->setStatusCode(StatusCode::SEE_OTHER);
        $this->response->addHeader('Location', $authorization->getUrl(
            $request->getBaseUrl() . $urlBuilder->build('processGitHubRedirectUri')
        ));

        return $this->response;
    }

    public function processGitHubLogInRedirectUri(
        Request $request,
        Authorization $authorization,
        UrlBuilder $urlBuilder,
        GateKeeper $gateKeeper,
        Session $session,
        UserStorage $storage
    ): Response {
        if (!$authorization->isStateValid($request->get('state'))) {
            return $this->buildErrorResponse($urlBuilder);
        }

        $result = $authorization->getAccessToken($request->get('code'));

        if (!isset($result['access_token'])) {
            return $this->buildErrorResponse($urlBuilder);
        }

        $userInformation = $authorization->getUserInformation($result['access_token']);

        $user = new User($userInformation['id'], $userInformation['login'], $userInformation['avatar_url']);

        $gateKeeper->authorize($user);

        $session->set('user', [
            'id'        => $user->getId(),
            'username'  => $user->getUsername(),
            'avatarUrl' => $user->getAvatarUrl(),
        ]);

        $storage->store($user);

        $this->response->setStatusCode(StatusCode::SEE_OTHER);
        $this->response->addHeader('Location', '/');

        return $this->response;
    }

    private function buildErrorResponse(UrlBuilder $urlBuilder): Response
    {
        $this->response->setStatusCode(StatusCode::FORBIDDEN);
        $this->response->addHeader('Location', $urlBuilder->build(''));

        return $this->response;
    }
}
