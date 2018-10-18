<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Feed\Create;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed;

class Dashboard
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(Html $template, Create $createForm, Feed $storage, GateKeeper $gateKeeper): Response
    {
        $this->response->setContent($template->renderPage('/dashboard/index.phtml', [
            'overview'   => $storage->getUserFeeds($gateKeeper->getUser()),
            'createForm' => $createForm,
        ]));

        return $this->response;
    }
}
