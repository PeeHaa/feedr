<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class Router
{
    private $routeCollector;

    private $dispatcherFactory;

    private $cacheFile;

    private $forceReload;

    public function __construct(
        RouteCollector $routeCollector,
        callable $dispatcherFactory,
        string $cacheFile,
        bool $forceReload = false
    ) {
        $this->routeCollector    = $routeCollector;
        $this->dispatcherFactory = $dispatcherFactory;
        $this->cacheFile         = $cacheFile;
        $this->forceReload       = $forceReload;
    }

    public function get(string $path, array $callback): Router
    {
        return $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, array $callback): Router
    {
        return $this->addRoute('POST', $path, $callback);
    }

    public function addRoute(string $verb, string $path, array $callback): Router
    {
        $this->routeCollector->addRoute($verb, $path, $callback);

        return $this;
    }

    public function getDispatcher(): Dispatcher
    {
        if ($this->forceReload || !file_exists($this->cacheFile)) {
            $dispatchData = $this->buildCache();
        } else {
            /** @noinspection PhpIncludeInspection */
            $dispatchData = require $this->cacheFile;
        }

        return call_user_func($this->dispatcherFactory, $dispatchData);
    }

    private function buildCache(): array
    {
        $dispatchData = $this->routeCollector->getData();

        file_put_contents($this->cacheFile, '<?php declare(strict_types=1);' . PHP_EOL . PHP_EOL . 'return ' . var_export($dispatchData, true) . ';');

        return $dispatchData;
    }
}
