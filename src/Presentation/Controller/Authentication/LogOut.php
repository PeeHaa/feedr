<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Authorization;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use CodeCollab\Http\Session\Session;
use PeeHaa\AwesomeFeed\Form\Authentication\LogOut as LogOutForm;

class LogOut
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function process(Request $request, Session $session, LogOutForm $form): Response
    {
        $form->bindRequest($request);

        if ($form->isValid()) {
            $session->destroy();
        }

        $this->response->setStatusCode(StatusCode::FOUND);
        $this->response->addHeader('Location', '/');

        return $this->response;
    }
}
