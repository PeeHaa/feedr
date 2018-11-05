<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Repository;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Repository\Create as CreateForm;
use PeeHaa\AwesomeFeed\Form\Repository\Search as SearchForm;
use PeeHaa\AwesomeFeed\GitHub\Repository;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\GitHub\Repository as Storage;
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
        SearchForm $searchForm,
        CreateForm $createForm,
        GateKeeper $gateKeeper,
        Storage $gitHubStorage,
        string $id
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

        $searchForm->bindRequest($request);
        $searchForm->validate();

        if (!$searchForm->isValid()) {
            $this->response->setContent(json_encode([
                'error' => [
                    'message' => 'Form is invalid',
                    'form'    => $searchForm->toArray(),
                ],
            ]));

            $this->response->setStatusCode(StatusCode::NOT_FOUND);

            return $this->response;
        }

        $repositories = $gitHubStorage
            ->search($searchForm['query']->getValue())
            ->filter(static function(Repository $repository) use ($feed) {
                return !$feed->isRepositoryAdded($repository);
            })
        ;

        $this->response->setContent(json_encode([
            'content' => $template->render('/feed/repository/search-result-modal.phtml', [
                'searchForm'   => $searchForm,
                'createForm'   => $createForm,
                'repositories' => $repositories,
                'feed'         => $feed,
            ]),
        ]));

        return $this->response;
    }
}
