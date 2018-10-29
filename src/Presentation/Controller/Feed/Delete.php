<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Feed;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Feed\Delete as Form;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;

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
        FeedStorage $storage,
        GateKeeper $gateKeeper,
        string $id
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid()) {
            return $this->response;
        }

        $feed = $storage->getById((int) $id);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $storage->deleteFeed($feed);

        $this->response->setContent(json_encode([
            'id' => $feed->getId(),
        ]));

        return $this->response;
    }
}
