<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Router;

use Auryn\Injector as Auryn;
use CodeCollab\Http\Response\Response;

class Injector
{
    private $injector;

    public function __construct(Auryn $injector)
    {
        $this->injector = $injector;
    }

    public function execute(callable $callback, array $vars): Response
    {
        $arguments = $this->resolveDependencies($callback, $vars);

        return call_user_func_array($callback, $arguments);
    }

    private function resolveDependencies(callable $callback, array $vars): array
    {
        $method = new \ReflectionMethod($callback[0], $callback[1]);

        $dependencies = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getClass() === null && !count($vars)) {
                break;
            }

            if ($parameter->getClass() === null && count($vars)) {
                $dependencies[] = array_shift($vars);

                continue;
            }

            $dependencies[] = $this->injector->make($parameter->getClass()->name);
        }

        return $dependencies;
    }
}
