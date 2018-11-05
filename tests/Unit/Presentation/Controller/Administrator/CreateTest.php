<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\Collection as UserCollection;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\Form\Administrator\Create as CreateForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Create;
use PeeHaa\AwesomeFeed\Storage\GitHub\User as GitHubApi;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    /** @var MockObject|Request */
    private $request;

    /** @var MockObject|CreateForm */
    private $createForm;

    /** @var MockObject|FeedStorage */
    private $feedStorage;

    /** @var MockObject|UserStorage */
    private $userStorage;

    /** @var MockObject|GitHubApi */
    private $gitHubApi;

    /** @var MockObject|GateKeeper */
    private $gateKeeper;

    public function setUp()
    {
        $this->response    = $this->createMock(Response::class);
        $this->request     = $this->createMock(Request::class);
        $this->createForm  = $this->createMock(CreateForm::class);
        $this->feedStorage = $this->createMock(FeedStorage::class);
        $this->userStorage = $this->createMock(UserStorage::class);
        $this->gitHubApi   = $this->createMock(GitHubApi::class);
        $this->gateKeeper  = $this->createMock(GateKeeper::class);

        $this->gateKeeper
            ->method('getUser')
            ->willReturn($this->createMock(User::class))
        ;
    }

    public function testProcessValidatesFormAndBailsOutWhenInvalid()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false)
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }

    public function testProcessBailsOutWhenUserIsNotPostedInRequest()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->request
            ->expects($this->once())
            ->method('post')
            ->willReturnCallback(function($key) {
                $this->assertSame($key, 'user');

                return '';
            })
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }

    public function testProcessRendersNotFoundWhenFeedIsNotFound()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->feedStorage
            ->method('getById')
            ->willReturn(null)
        ;

        $this->request
            ->expects($this->once())
            ->method('post')
            ->willReturn(true)
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('NOT FOUND', $content);
            })
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }

    public function testProcessRendersNotFoundWhenUserHasNoAccess()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $feed = $this->createMock(Feed::class);

        $feed
            ->expects($this->once())
            ->method('hasUserAccess')
            ->willReturn(false)
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('getById')
            ->willReturn($feed)
        ;

        $this->request
            ->expects($this->once())
            ->method('post')
            ->willReturn(true)
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('NOT FOUND', $content);
            })
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }

    public function testProcessCreatesUsers()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $feed = $this->createMock(Feed::class);

        $feed
            ->expects($this->once())
            ->method('hasUserAccess')
            ->willReturn(true)
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('getById')
            ->willReturn($feed)
        ;

        $this->request
            ->method('post')
            ->willReturn(['user1', 'user2'])
        ;

        $userCollection = new UserCollection();

        $this->gitHubApi
            ->expects($this->once())
            ->method('getUsersByUsernames')
            ->willReturnCallback(function($user1, $user2) use ($userCollection) {
                $this->assertSame('user1', $user1);
                $this->assertSame('user2', $user2);

                return $userCollection;
            })
        ;

        $this->userStorage
            ->expects($this->once())
            ->method('storeCollection')
            ->willReturnCallback(function($passedCollection) use ($userCollection) {
                $this->assertSame($userCollection, $passedCollection);
            })
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('addAdmins')
            ->willReturnCallback(function($feedId, $passedCollection) use ($userCollection) {
                $this->assertSame(12, $feedId);
                $this->assertSame($userCollection, $passedCollection);
            })
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }

    public function testProcessSetsResponseData()
    {
        $this->createForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->createForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->createForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $feed = $this->createMock(Feed::class);

        $feed
            ->expects($this->once())
            ->method('hasUserAccess')
            ->willReturn(true)
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('getById')
            ->willReturn($feed)
        ;

        $this->request
            ->method('post')
            ->willReturn(['user1', 'user2'])
        ;

        $userCollection = new UserCollection();

        $this->gitHubApi
            ->expects($this->once())
            ->method('getUsersByUsernames')
            ->willReturn($userCollection)
        ;

        $this->userStorage
            ->expects($this->once())
            ->method('storeCollection')
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('addAdmins')
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertArrayHasKey('administrators', json_decode($content, true));
                $this->assertArrayHasKey('feed', json_decode($content, true));
            })
        ;

        $controller = new Create($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->createForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gitHubApi,
            $this->gateKeeper,
            '12'
        ));
    }
}
