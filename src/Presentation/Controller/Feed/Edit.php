<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Feed;

use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed;

class Edit
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(Html $template, Feed $storage, UrlBuilder $urlBuilder, string $id, string $slug): Response
    {
        $feed = $storage->getById((int) $id);

        if ($feed === null) {
            return (new Error($this->response))->notFound();
        }

        if ($feed->getSlug() !== $slug) {
            $this->response->setStatusCode(StatusCode::PERMANENTLY_REDIRECT);
            $this->response->addHeader('Location', $urlBuilder->build('editFeed', [
                'id'   => $feed->getId(),
                'slug' => $feed->getSlug(),
            ]));

            return $this->response;
        }

        $this->response->setContent('FEED CREATED!');

        return $this->response;
    }
}
