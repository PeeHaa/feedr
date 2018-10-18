<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Router;

use Auryn\Injector as Auryn;
use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Session\Session;
use FastRoute\Dispatcher;
use PeeHaa\AwesomeFeed\Router\Exception\ActionNotFound;
use PeeHaa\AwesomeFeed\Router\Exception\ControllerNotFound;

class FrontController
{
    private $router;

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $response;

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $session;

    private $auryn;

    private $injector;

    public function __construct(Router $router, Response $response, Session $session, Auryn $auryn, Injector $injector)
    {
        $this->router   = $router;
        $this->response = $response;
        $this->session  = $session;
        $this->auryn    = $auryn;
        $this->injector = $injector;
    }

    public function run(Request $request)
    {
        $dispatcher = $this->router->getDispatcher();
        $routeInfo  = $dispatcher->dispatch($request->server('REQUEST_METHOD'), $request->server('REQUEST_URI_PATH'));

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $routeInfo = $this->getNotFoundRoute($dispatcher);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $routeInfo = $this->runMethodNotAllowed($dispatcher);
                break;

            case Dispatcher::FOUND:
                break;
        }

        $response = $this->runRoute($routeInfo);

        $response->send();
    }

    private function runRoute(array $routeInfo): Response
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
        //var_dump($routeInfo);die;
        [$_, $callback, $vars] = $routeInfo;

        $vars = array_filter($vars, static function($var) {
            return strpos($var, '_') !== 0;
        }, ARRAY_FILTER_USE_KEY);

        if (!class_exists($callback[0])) {
            throw new ControllerNotFound(
                'Trying to instantiate a non existent controller (`' . $callback[0] . '`)'
            );
        }

        $controller = $this->auryn->make($callback[0]);

        if (!method_exists($controller, $callback[1])) {
            throw new ActionNotFound(
                'Trying to call a non existent action (`' . $callback[0] . '::' . $callback[1] . '`)'
            );
        }

        return $this->injector->execute([$controller, $callback[1]], array_map('urldecode', $vars));
    }

    private function getNotFoundRoute(Dispatcher $dispatcher): array
    {
        return $dispatcher->dispatch('GET', '/not-found');
    }

    private function runMethodNotAllowed(Dispatcher $dispatcher): array
    {
        return $dispatcher->dispatch('GET', '/method-not-allowed');
    }
}
