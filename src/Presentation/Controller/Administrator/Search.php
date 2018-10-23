<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Form\Administrator\Search as Form;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\GitHub\User as Storage;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed;

class Search
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(
        Html $template,
        Feed $storage,
        Request $request,
        Form $form,
        GateKeeper $gateKeeper,
        Storage $gitHubStorage,
        string $id,
        string $slug
    ): Response {
        $feed = $storage->getById((int) $id);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            $this->response->setContent(json_encode([
                'error' => [
                    'message' => 'Feed not found',
                ],
            ]));

            $this->response->setStatusCode(StatusCode::NOT_FOUND);

            return $this->response;
        }

        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid()) {
            $this->response->setContent(json_encode([
                'error' => [
                    'message' => 'Form is invalid',
                    'form'    => $form->toArray(),
                ],
            ]));

            $this->response->setStatusCode(StatusCode::NOT_FOUND);

            return $this->response;
        }

        $users = $gitHubStorage
            ->search($form['query']->getValue())
            ->filter(function(User $user) use ($feed, $gateKeeper) {
                return !$feed->hasUserAccess($user);
            })
        ;

        $this->response->setContent(json_encode([
            'content' => $template->render('/feed/administrator/search-result-modal.phtml', [
                'searchForm' => $form,
                'users'      => $users,
            ]),
        ]));

        return $this->response;
    }
}
