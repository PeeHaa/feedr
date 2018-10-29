<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete as Form;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;

class Delete
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function process(
        Request $request,
        Form $form,
        FeedStorage $feedStorage,
        UserStorage $userStorage,
        GateKeeper $gateKeeper,
        string $feedId,
        string $feedSlug,
        string $userId
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid()) {
            return $this->response;
        }

        $feed = $feedStorage->getById((int) $feedId);
        $user = $userStorage->getById((int) $userId);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $feedStorage->deleteAdmin($feed, $user);

        $this->response->setContent(json_encode([
            'id'         => $user->getId(),
            'selfDelete' => $user === $gateKeeper->getUser()->getId(),
        ]));

        return $this->response;
    }
}
