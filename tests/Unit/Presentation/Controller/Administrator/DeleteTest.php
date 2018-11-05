<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller\Administrator;

use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete as DeleteForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Delete;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    /** @var MockObject|Request */
    private $request;

    /** @var MockObject|DeleteForm */
    private $deleteForm;

    /** @var MockObject|FeedStorage */
    private $feedStorage;

    /** @var MockObject|UserStorage */
    private $userStorage;

    /** @var MockObject|GateKeeper */
    private $gateKeeper;

    public function setUp()
    {
        $this->response    = $this->createMock(Response::class);
        $this->request     = $this->createMock(Request::class);
        $this->deleteForm  = $this->createMock(DeleteForm::class);
        $this->feedStorage = $this->createMock(FeedStorage::class);
        $this->userStorage = $this->createMock(UserStorage::class);
        $this->gateKeeper  = $this->createMock(GateKeeper::class);

        $this->gateKeeper
            ->method('getUser')
            ->willReturn($this->createMock(User::class))
        ;
    }

    public function testProcessValidatesFormAndBailsOutWhenInvalid()
    {
        $this->deleteForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false)
        ;

        $controller = new Delete($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testProcessRendersNotFoundWhenFeedIsNotFound()
    {
        $this->deleteForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->feedStorage
            ->method('getById')
            ->willReturn(null)
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('NOT FOUND', $content);
            })
        ;

        $controller = new Delete($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testProcessRendersNotFoundWhenUserHasNoAccess()
    {
        $this->deleteForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->deleteForm
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

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertSame('NOT FOUND', $content);
            })
        ;

        $controller = new Delete($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testProcessDeletesAdmin()
    {
        $this->deleteForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->deleteForm
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

        $this->userStorage
            ->expects($this->once())
            ->method('getById')
            ->willReturn($this->createMock(User::class))
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('deleteAdmin')
            ->willReturnCallback(function($feed, $user) {
                $this->assertInstanceOf(Feed::class, $feed);
                $this->assertInstanceOf(User::class, $user);
            })
        ;

        $controller = new Delete($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testProcessRendersResponseData()
    {
        $this->deleteForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->deleteForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->deleteForm
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

        $this->userStorage
            ->expects($this->once())
            ->method('getById')
            ->willReturn($this->createMock(User::class))
        ;

        $this->feedStorage
            ->expects($this->once())
            ->method('deleteAdmin')
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertArrayHasKey('id', json_decode($content, true));
                $this->assertArrayHasKey('selfDelete', json_decode($content, true));
            })
        ;

        $controller = new Delete($this->response);

        $this->assertSame($this->response, $controller->process(
            $this->request,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }
}
