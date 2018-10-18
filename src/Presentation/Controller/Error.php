<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller;

use CodeCollab\Http\Response\Response;

class Error
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function notFound(): Response
    {
        $this->response->setContent('NOT FOUND');

        return $this->response;
    }

    public function methodNotAllowed(): Response
    {
        $this->response->setContent('METHOD NOT ALLOWED');

        return $this->response;
    }
}
