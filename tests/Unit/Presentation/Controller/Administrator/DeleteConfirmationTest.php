<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller\Administrator;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete as DeleteForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\DeleteConfirmation;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PeeHaa\AwesomeFeed\Storage\Postgres\User as UserStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteConfirmationTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    /** @var MockObject|Html */
    private $template;

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
        $this->template    = $this->createMock(Html::class);
        $this->deleteForm  = $this->createMock(DeleteForm::class);
        $this->feedStorage = $this->createMock(FeedStorage::class);
        $this->userStorage = $this->createMock(UserStorage::class);
        $this->gateKeeper  = $this->createMock(GateKeeper::class);

        $this->gateKeeper
            ->method('getUser')
            ->willReturn($this->createMock(User::class))
        ;
    }

    public function testRenderRendersNotFoundWhenFeedIsNotFound()
    {
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

        $controller = new DeleteConfirmation($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testRenderRendersNotFoundWhenUserHasNoAccess()
    {
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

        $controller = new DeleteConfirmation($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }

    public function testRenderRendersResponseData()
    {
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

        $this->template
            ->method('render')
            ->willReturnCallback(function($template, $content) {
                $this->assertSame('/feed/administrator/delete-confirmation-modal.phtml', $template);

                $this->assertArrayHasKey('deleteForm', $content);
                $this->assertArrayHasKey('feed', $content);
                $this->assertArrayHasKey('user', $content);
                $this->assertArrayHasKey('selfDelete', $content);

                return 'TheContent';
            })
        ;

        $controller = new DeleteConfirmation($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->deleteForm,
            $this->feedStorage,
            $this->userStorage,
            $this->gateKeeper,
            '12',
            '3'
        ));
    }
}
