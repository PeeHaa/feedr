<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Administrator\Create as Form;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Storage\GitHub\User as GitHubApi;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;

class Create
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function process(
        Request $request,
        Form $form,
        FeedStorage $storage,
        UserStorage $userStorage,
        GitHubApi $gitHubStorage,
        GateKeeper $gateKeeper,
        string $id
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid() || !$request->post('user')) {
            return $this->response;
        }

        $feed = $storage->getById((int) $id);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $users = $gitHubStorage->getUsersByUsernames(...$request->post('user'));

        $userStorage->storeCollection($users);

        $storage->addAdmins((int) $id, $users);

        $this->response->setContent(json_encode([
            'administrators' => $users->toArray(),
            'feed'           => $feed->toArray(),
        ]));

        return $this->response;
    }
}
