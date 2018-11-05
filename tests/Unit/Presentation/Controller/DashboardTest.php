<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller;

use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Collection;
use PeeHaa\AwesomeFeed\Form\Feed\Create as CreateFeedForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Dashboard;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    /** @var MockObject|Html */
    private $template;

    /** @var MockObject|CreateFeedForm */
    private $createFeedForm;

    /** @var MockObject|FeedStorage */
    private $feedStorage;

    /** @var MockObject|GateKeeper */
    private $gateKeeper;

    public function setUp()
    {
        $this->response       = $this->createMock(Response::class);
        $this->template       = $this->createMock(Html::class);
        $this->createFeedForm = $this->createMock(CreateFeedForm::class);
        $this->feedStorage    = $this->createMock(FeedStorage::class);
        $this->gateKeeper     = $this->createMock(GateKeeper::class);

        $this->gateKeeper
            ->method('getUser')
            ->willReturn($this->createMock(User::class))
        ;
    }

    public function testRenderUsesCorrectTemplate()
    {
        $this->response
            ->expects($this->once())
            ->method('setContent')
        ;

        $this->template
            ->expects($this->once())
            ->method('renderPage')
            ->willReturnCallback(function($template) {
                $this->assertSame('/dashboard/index.phtml', $template);

                return 'TheContent';
            })
        ;

        $controller = new Dashboard($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->createFeedForm,
            $this->feedStorage,
            $this->gateKeeper
        ));
    }

    public function testRenderSetsOverviewData()
    {
        $this->response
            ->expects($this->once())
            ->method('setContent')
        ;

        $this->feedStorage
            ->method('getUserFeeds')
            ->willReturn($this->createMock(Collection::class))
        ;

        $this->template
            ->expects($this->once())
            ->method('renderPage')
            ->willReturnCallback(function($template, $extraData) {
                $this->assertSame('/dashboard/index.phtml', $template);

                $this->assertArrayHasKey('overview', $extraData);
                $this->assertSame($this->createFeedForm, $extraData['createForm']);

                return 'TheContent';
            })
        ;

        $controller = new Dashboard($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->createFeedForm,
            $this->feedStorage,
            $this->gateKeeper
        ));
    }

    public function testRenderSetsForm()
    {
        $this->response
            ->expects($this->once())
            ->method('setContent')
        ;

        $this->template
            ->expects($this->once())
            ->method('renderPage')
            ->willReturnCallback(function($template, $extraData) {
                $this->assertSame('/dashboard/index.phtml', $template);

                $this->assertArrayHasKey('createForm', $extraData);
                $this->assertSame($this->createFeedForm, $extraData['createForm']);

                return 'TheContent';
            })
        ;

        $controller = new Dashboard($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->createFeedForm,
            $this->feedStorage,
            $this->gateKeeper
        ));
    }
}
