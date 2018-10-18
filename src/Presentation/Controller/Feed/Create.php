<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Feed;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Feed\Create as CreateForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Dashboard;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed;

class Create
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function process(
        Html $template,
        CreateForm $form,
        Request $request,
        GateKeeper $gateKeeper,
        Feed $storage,
        UrlBuilder $urlBuilder
    ): Response {
        $form->bindRequest($request);
        $form->validate();

        if (!$form->isValid()) {
            return (new Dashboard($this->response))->render($template, $form, $storage, $gateKeeper);
        }

        $feed = $storage->create($form, $gateKeeper->getUser());

        $this->response->setStatusCode(StatusCode::FOUND);
        $this->response->addHeader('Location', $urlBuilder->build('editFeed', [
            'id'   => $feed->getId(),
            'slug' => $feed->getSlug(),
        ]));

        return $this->response;
    }
}
