<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Administrator;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;

class DeleteConfirmation
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(
        Html $template,
        Delete $form,
        FeedStorage $feedStorage,
        UserStorage $userStorage,
        GateKeeper $gateKeeper,
        string $feedId,
        string $userId
    ): Response {
        $feed = $feedStorage->getById((int) $feedId);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $user = $userStorage->getById((int) $userId);

        $this->response->setContent($template->render('/feed/administrator/delete-confirmation-modal.phtml', [
            'deleteForm' => $form,
            'feed'       => $feed,
            'user'       => $user,
            'selfDelete' => $user->getId() === $gateKeeper->getUser()->getId(),
        ]));

        return $this->response;
    }
}
