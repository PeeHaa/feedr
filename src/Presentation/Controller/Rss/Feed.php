<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Controller\Rss;

use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\StatusCode;
use PeeHaa\AwesomeFeed\Presentation\Template\Xml;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;

class Feed
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function render(Xml $template, FeedStorage $feedStorage, UrlBuilder $urlBuilder, string $id, string $slug)
    {
        $feed = $feedStorage->getById((int) $id);

        if ($feed->getSlug() !== $slug) {
            $this->response->setStatusCode(StatusCode::PERMANENTLY_REDIRECT);
            $this->response->addHeader('Location', $urlBuilder->build('rss', [
                'id'   => $feed->getId(),
                'slug' => $feed->getSlug(),
            ]));

            return $this->response;
        }

        $this->response->setContent('<?xml version="1.0" encoding="utf-8"?>' . $template->render('/rss/feed.pxml', [
            'feed' => $feed,
        ]));

        return $this->response;
    }
}
