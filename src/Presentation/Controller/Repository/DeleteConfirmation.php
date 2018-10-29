<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Repository;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\Repository as RepositoryStorage;

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
        RepositoryStorage $repositoryStorage,
        GateKeeper $gateKeeper,
        string $feedId,
        string $feedSlug,
        string $repositoryId
    ): Response {
        $feed = $feedStorage->getById((int) $feedId);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $repository = $repositoryStorage->getById((int) $repositoryId);

        $this->response->setContent($template->render('/feed/repository/delete-confirmation-modal.phtml', [
            'deleteForm' => $form,
            'feed'       => $feed,
            'repository' => $repository,
        ]));

        return $this->response;
    }
}
