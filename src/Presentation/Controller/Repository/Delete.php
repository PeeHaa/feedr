<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Repository;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete as Form;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\Repository as RepositoryStorage;

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
        RepositoryStorage $repositoryStorage,
        GateKeeper $gateKeeper,
        string $feedId,
        string $repositoryId
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid()) {
            return $this->response;
        }

        $feed       = $feedStorage->getById((int) $feedId);
        $repository = $repositoryStorage->getById((int) $repositoryId);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $feedStorage->deleteRepository($feed, $repository);

        $this->response->setContent(json_encode([
            'repository' => $repository->toArray(),
        ]));

        return $this->response;
    }
}
