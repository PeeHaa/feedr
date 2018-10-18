<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed;

use Amp\Artax\Client;
use Amp\Artax\DefaultClient;
use Auryn\Injector;
use CodeCollab\CsrfToken\Generator\Generator;
use CodeCollab\CsrfToken\Generator\RandomBytes32;
use CodeCollab\CsrfToken\Handler;
use CodeCollab\CsrfToken\Storage\Storage;
use CodeCollab\CsrfToken\Token;
use CodeCollab\Encryption\Decryptor;
use CodeCollab\Encryption\Defusev2\Encryptor as DefuseEncryptor;
use CodeCollab\Encryption\Defusev2\Decryptor as DefuseDecryptor;
use CodeCollab\Encryption\Encryptor;
use CodeCollab\Http\Cookie\Factory as CookieFactory;
use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Session\Native as NativeSession;
use CodeCollab\Http\Session\Session;
use CodeCollab\I18n\FileTranslator;
use CodeCollab\I18n\Translator;
use FastRoute\DataGenerator;
use FastRoute\RouteParser;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as RouteDispatcher;
use FastRoute\RouteParser\Std as StandardRouteParser;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\GitHub\Credentials;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Router\FrontController;
use PeeHaa\AwesomeFeed\Router\Manager as RouteManager;
use PeeHaa\AwesomeFeed\Router\Router;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\Storage\TokenSession;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/vendor/autoload.php';

/**
 * Set up dependency injection
 */
$auryn = new Injector();
$auryn->share($auryn);

/**
 * Prevent further execution when on CLI
 */
if (php_sapi_name() === 'cli') {
    return;
}

/**
 * Set up error handling
 */
$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

/**
 * Set up the environment
 */
$configuration = require_once __DIR__ . '/config/config.php';

/**
 * Set up encryption
 */
$auryn->share(Decryptor::class);
$auryn->alias(Encryptor::class, DefuseEncryptor::class);
$auryn->alias(Decryptor::class, DefuseDecryptor::class);
$auryn->define(Encryptor::class, [':key' => file_get_contents(__DIR__ . '/config/encryption.key')]);
$auryn->define(Decryptor::class, [':key' => file_get_contents(__DIR__ . '/config/encryption.key')]);

/**
 * Set up the response object
 */
$auryn->share(Response::class);

/**
 * Set up the request object
 */
$auryn->share(Request::class);
$request = $auryn->make(Request::class, [
    ':server'  => $_SERVER,
    ':get'     => $_GET,
    ':post'    => $_POST,
    ':files'   => $_FILES,
    ':cookies' => $_COOKIE,
    ':input'   => file_get_contents('php://input'),
]);

/**
 * Set up cookies
 */
$auryn->define(CookieFactory::class, [
    ':domain' => $request->server('SERVER_NAME'),
    ':secure' => $request->isEncrypted(),
]);

/**
 * Set up the session
 */
$auryn->share(Session::class);
$auryn->alias(Session::class, NativeSession::class);
$auryn->define(NativeSession::class, [
    ':path'   => '/',
    ':domain' => $request->server('SERVER_NAME'),
    ':secure' => $request->isEncrypted()
]);
$session = $auryn->make(Session::class);

/**
 * Set up the CSRF tokens
 */
$auryn->alias(Token::class, Handler::class);
$auryn->alias(Generator::class, RandomBytes32::class);
$auryn->alias(Storage::class, TokenSession::class);

/**
 * Set up the database connection
 */
$auryn->share(\PDO::class);
$auryn->delegate(\PDO::class, function() use ($configuration) {
    $dbConnection = new \PDO(
        sprintf('pgsql:dbname=%s;host=%s', $configuration['database']['name'], $configuration['database']['host']),
        $configuration['database']['username'],
        $configuration['database']['password']
    );

    $dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    return $dbConnection;
});

/**
 * Set up the gate keeper
 */
$auryn->share(GateKeeper::class);
$auryn->delegate(GateKeeper::class, function() use ($session) {
    $gateKeeper = new GateKeeper();

    if ($session->exists('user')) {
        $gateKeeper->authorize(new User(
            $session->get('user')['id'],
            $session->get('user')['username'],
            $session->get('user')['avatarUrl']
        ));
    }

    return $gateKeeper;
});
$gateKeeper = $auryn->make(GateKeeper::class);

/**
 * Set up the router
 */
$cacheFile = $gateKeeper->isAuthorized() ? __DIR__ . '/cache/routes.user.php' : __DIR__ . '/cache/routes.php';
$auryn->share(Router::class);
$auryn->alias(RouteParser::class, StandardRouteParser::class);
$auryn->alias(DataGenerator::class, GroupCountBasedDataGenerator::class);
$auryn->define(Router::class, [
    ':dispatcherFactory' => function($dispatchData) {
        return new RouteDispatcher($dispatchData);
    },
    ':cacheFile'   => $cacheFile,
    ':forceReload' => $configuration['reloadRoutes'],
]);
$auryn->share(RouteManager::class);

/**
 * Setup the url builder
 */
$auryn->share(UrlBuilder::class);
$auryn->define(UrlBuilder::class, [':globalDefaults' => []]);

/**
 * Set up translations
 */
$auryn->share(Translator::class);
$auryn->alias(Translator::class, FileTranslator::class);
$auryn->define(FileTranslator::class, [
    ':translationDirectory' => __DIR__ . '/texts',
    ':languageCode'         => 'en_US',
]);

/**
 * Set up templating
 */
$auryn->share(Html::class);
$auryn->define(Html::class, [
    ':basePage'     => '/page.phtml',
    ':templatePath' => __DIR__ . '/templates',
]);

/**
 * Set up teh HTTP client
 */
$auryn->alias(Client::class, DefaultClient::class);

/**
 * Set up the GitHub API
 */
$auryn->define(Credentials::class, [
    ':clientId'     => $configuration['gitHub']['clientId'],
    ':clientSecret' => $configuration['gitHub']['clientSecret'],
]);

/**
 * Load routes
 */
require __DIR__ . '/routes.php';

/**
 * Set up the front controller
 */
$frontController = $auryn->make(FrontController::class);

/**
 * Run the application
 */
$frontController->run($request);
