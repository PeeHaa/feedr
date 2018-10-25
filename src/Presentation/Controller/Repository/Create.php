<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Repository;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Form\Repository\Create as Form;
use PeeHaa\AwesomeFeed\Storage\GitHub\Repository as GitHubApi;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\Repository as RepositoryStorage;
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
        RepositoryStorage $repositoryStorage,
        GitHubApi $gitHubStorage,
        string $id
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid() || !$request->post('repository')) {
            return $this->response;
        }

        $repositories = $gitHubStorage->getRepositoriesByIdentifiers(...$request->post('repository'));

        foreach ($repositories as $repository) {
            $userStorage->store($repository->getOwner());

            $repositoryStorage->store($repository);
        }

        $storage->addRepositories((int) $id, $repositories);

        $this->response->setContent(json_encode($repositories->toArray()));

        return $this->response;
    }
}
