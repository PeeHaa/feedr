<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Router;

class Manager
{
    private $router;

    private $urlBuilder;

    public function __construct(Router $router, UrlBuilder $urlBuilder)
    {
        $this->router     = $router;
        $this->urlBuilder = $urlBuilder;
    }

    public function get(string $name, string $path, array $callback): Manager
    {
        $this->router->get($path, $callback);
        $this->urlBuilder->addUrl($name, $path);

        return $this;
    }

    public function post(string $name, string $path, array $callback): Manager
    {
        $this->router->post($path, $callback);
        $this->urlBuilder->addUrl($name, $path);

        return $this;
    }

    public function addRoute(string $name, string $verb, string $path, array $callback): Manager
    {
        $this->router->addRoute($verb, $path, $callback);
        $this->urlBuilder->addUrl($name, $path);

        return $this;
    }
}
