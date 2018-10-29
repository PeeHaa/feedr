#!/usr/bin/env php
<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Bin;

use Amp\Http\Server\Router;
use Amp\Http\Server\Server;
use Amp\Http\Server\Websocket\Application;
use Amp\Http\Server\Websocket\Websocket;
use Amp\Loop;
use Auryn\Injector;
use PeeHaa\AwesomeFeed\Queue\Queue;
use PeeHaa\AwesomeFeed\WebSocket\Configuration;
use PeeHaa\AwesomeFeed\WebSocket\Controller;
use Psr\Log\NullLogger;
use function Amp\Socket\listen;
use function Amp\asyncCall;

require_once __DIR__ . '/../bootstrap.php';

/** @var Injector $auryn */
$webSocketConfiguration = $auryn->make(Configuration::class);

$servers = [
    listen(sprintf(
        '%s:%d',
        $webSocketConfiguration->getInternalHostname(),
        $webSocketConfiguration->getInternalPort()
    )),
];

$auryn->share(Controller::class);
$auryn->alias(Application::class, Controller::class);

$router = new Router();

$router->addRoute('GET', '/live-releases', $auryn->make(Websocket::class));

$server = new Server($servers, $router, new NullLogger());
$queue  = $auryn->make(Queue::class);

Loop::run(function () use ($server, $queue) {
    asyncCall(function() use ($server) {
        yield $server->start();
    });

    asyncCall(function() use ($queue) {
        $queue->start();
    });
});
