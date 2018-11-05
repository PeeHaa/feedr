<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Feed;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Form\Feed\Delete;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed;

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
        Feed $storage,
        GateKeeper $gateKeeper,
        string $id
    ): Response {
        $feed = $storage->getById((int) $id);

        if ($feed === null || !$feed->hasUserAccess($gateKeeper->getUser())) {
            return (new Error($this->response))->notFound();
        }

        $this->response->setContent($template->render('/feed/delete-confirmation-modal.phtml', [
            'deleteForm' => $form,
            'feed'       => $feed,
        ]));

        return $this->response;
    }
}
