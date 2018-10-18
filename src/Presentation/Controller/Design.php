<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;

class Design
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(Html $template): Response
    {
        $this->response->setContent($template->renderPage('/design/index.phtml'));

        return $this->response;
    }
}