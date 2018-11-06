<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Form\Administrator\Create as CreateForm;
use PeeHaa\AwesomeFeed\Form\Administrator\Search as Form;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\GitHub\User as Storage;
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
        Form $form,
        CreateForm $createForm,
        GateKeeper $gateKeeper,
        Storage $gitHubStorage,
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

        $users = $gitHubStorage
            ->search($form['query']->getValue())
            ->filter(static function(User $user) use ($feed) {
                return !$feed->hasUserAccess($user);
            })
        ;

        $this->response->setContent(json_encode([
            'content' => $template->render('/feed/administrator/search-result-modal.phtml', [
                'searchForm' => $form,
                'createForm' => $createForm,
                'users'      => $users,
                'feed'       => $feed,
            ]),
        ]));

        return $this->response;
    }
}
