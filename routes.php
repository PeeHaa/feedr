<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed;

use Auryn\Injector;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Delete as DeleteAdministrator;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\DeleteConfirmation as DeleteAdministratorConfirmation;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Create as CreateAdministrators;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Search as SearchUser;
use PeeHaa\AwesomeFeed\Presentation\Controller\Authorization\LogIn;
use PeeHaa\AwesomeFeed\Presentation\Controller\Authorization\LogOut;
use PeeHaa\AwesomeFeed\Presentation\Controller\Dashboard;
use PeeHaa\AwesomeFeed\Presentation\Controller\Error;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\Create as CreateFeed;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\Delete as DeleteFeed;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\DeleteConfirmation as DeleteFeedConfirmation;
use PeeHaa\AwesomeFeed\Presentation\Controller\Feed\Edit as EditFeed;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\Create as CreateRepository;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\Delete as DeleteRepository;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\DeleteConfirmation as DeleteRepositoryConfirmation;
use PeeHaa\AwesomeFeed\Presentation\Controller\Repository\Search as SearchRepository;
use PeeHaa\AwesomeFeed\Presentation\Controller\Rss\Feed;
use PeeHaa\AwesomeFeed\Router\Manager as RouteManager;

/** @var Injector $auryn */
$gateKeeper = $auryn->make(GateKeeper::class);
$router     = $auryn->make(RouteManager::class);

$router->get('renderNotFound', '/not-found', [Error::class, 'notFound']);
$router->get('renderMethodNotAllowed', '/method-not-allowed', [Error::class, 'methodNotAllowed']);

$router->get('rss', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}', [Feed::class, 'render']);

if (!$gateKeeper->isAuthorized()) {
    $router->get('home', '/', [LogIn::class, 'render']);
    $router->get('renderLogin', '/login', [LogIn::class, 'render']);
    $router->post('processGitHubLogin', '/github/login', [LogIn::class, 'processGitHubLogIn']);
    $router->get('processGitHubRedirectUri', '/github/login', [LogIn::class, 'processGitHubLogInRedirectUri']);
}

if ($gateKeeper->isAuthorized()) {
    $router->get('home', '/', [Dashboard::class, 'render']);
    $router->post('createFeed', '/feeds/create', [CreateFeed::class, 'process']);
    $router->get('editFeed', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/edit', [EditFeed::class, 'render']);
    $router->get('deleteFeedConfirmation', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/delete', [DeleteFeedConfirmation::class, 'render']);
    $router->post('deleteFeed', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/delete', [DeleteFeed::class, 'process']);
    $router->post('searchUsers', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/administrators/search', [SearchUser::class, 'render']);
    $router->post('addAdministrators', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/administrators/create', [CreateAdministrators::class, 'process']);
    $router->get('deleteAdministratorConfirmation', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/administrators/{userId:\d+}/delete', [DeleteAdministratorConfirmation::class, 'render']);
    $router->post('deleteAdministrator', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/administrators/{userId:\d+}/delete', [DeleteAdministrator::class, 'process']);
    $router->post('searchRepositories', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/repositories/search', [SearchRepository::class, 'render']);
    $router->post('addRepositories', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/repositories/create', [CreateRepository::class, 'process']);
    $router->get('deleteRepositoryConfirmation', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/repositories/{repositoryId:\d+}/delete', [DeleteRepositoryConfirmation::class, 'render']);
    $router->post('deleteRepository', '/feeds/{id:\d+}/{slug:[a-z0-9\-\._]+}/repositories/{repositoryId:\d+}/delete', [DeleteRepository::class, 'process']);
    $router->post('logout', '/logout', [LogOut::class, 'process']);
}
