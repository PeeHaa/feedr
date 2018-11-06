<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Presentation\Controller\Administrator;

use CodeCollab\CsrfToken\Token;
use CodeCollab\Form\Field\Text as TextField;
use CodeCollab\Http\Request\Request;
use CodeCollab\Http\Response\Response;
use PeeHaa\AwesomeFeed\Authentication\Collection as UserCollection;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Collection as FeedCollection;
use PeeHaa\AwesomeFeed\Feed\Feed;
use PeeHaa\AwesomeFeed\Form\Administrator\Create as CreateForm;
use PeeHaa\AwesomeFeed\Form\Administrator\Search as SearchForm;
use PeeHaa\AwesomeFeed\Presentation\Controller\Administrator\Search;
use PeeHaa\AwesomeFeed\Presentation\Template\Html;
use PeeHaa\AwesomeFeed\Storage\GitHub\User as GitHubApi;
use PeeHaa\AwesomeFeed\Storage\Postgres\Feed as FeedStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    /** @var MockObject|Response */
    private $response;

    /** @var MockObject|Html */
    private $template;

    /** @var MockObject|FeedStorage */
    private $feedStorage;

    /** @var MockObject|Request */
    private $request;

    /** @var MockObject|SearchForm */
    private $searchForm;

    /** @var MockObject|CreateForm */
    private $createForm;

    /** @var MockObject|GateKeeper */
    private $gateKeeper;

    /** @var MockObject|GitHubApi */
    private $gitHubApi;

    /** @var MockObject|Token */
    private $csrfToken;

    public function setUp()
    {
        $this->response    = $this->createMock(Response::class);
        $this->template    = $this->createMock(Html::class);
        $this->feedStorage = $this->createMock(FeedStorage::class);
        $this->request     = $this->createMock(Request::class);
        $this->searchForm  = $this->createMock(SearchForm::class);
        $this->createForm  = $this->createMock(CreateForm::class);
        $this->gateKeeper  = $this->createMock(GateKeeper::class);
        $this->gitHubApi   = $this->createMock(GitHubApi::class);
        $this->csrfToken   = $this->createMock(Token::class);

        $this->gateKeeper
            ->method('getUser')
            ->willReturn($this->createMock(User::class))
        ;
    }

    public function testProcessValidatesFormAndBailsOutWhenInvalid()
    {
        $this->searchForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->searchForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->searchForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false)
        ;

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $this->searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function testProcessRendersNotFoundWhenFeedIsNotFound()
    {
        $this->searchForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->searchForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->searchForm
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

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $this->searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function testProcessRendersNotFoundWhenUserHasNoAccess()
    {
        $this->searchForm
            ->expects($this->once())
            ->method('bindRequest')
            ->willReturnCallback(function($request) {
                $this->assertSame($this->request, $request);
            })
        ;

        $this->searchForm
            ->expects($this->once())
            ->method('validate')
        ;

        $this->searchForm
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

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $this->searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function testProcessPassesSearchQueryToGitHubApi()
    {
        $searchForm = new class($this->csrfToken) extends SearchForm {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            public function setupFields()
            {
                $this->addField(new TextField('query'));
            }
        };

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
            ->method('postArray')
            ->willReturn(['query' => 'SearchQuery'])
        ;

        $userCollection = new UserCollection();

        $this->gitHubApi
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(function($searchQuery) use ($userCollection) {
                $this->assertSame('SearchQuery', $searchQuery);

                return $userCollection;
            })
        ;

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function testProcessFiltersExistingUsers()
    {
        $searchForm = new class($this->csrfToken) extends SearchForm {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            public function setupFields()
            {
                $this->addField(new TextField('query'));
            }
        };

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
            ->method('postArray')
            ->willReturn(['query' => 'SearchQuery'])
        ;

        $userCollection = $this->createMock(UserCollection::class);

        $userCollection
            ->expects($this->once())
            ->method('filter')
        ;

        $this->gitHubApi
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(function($searchQuery) use ($userCollection) {
                $this->assertSame('SearchQuery', $searchQuery);

                return $userCollection;
            })
        ;

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function testProcessSetsResponseData()
    {
        $searchForm = new class($this->csrfToken) extends SearchForm {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            public function setupFields()
            {
                $this->addField(new TextField('query'));
            }
        };

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
            ->method('postArray')
            ->willReturn(['query' => 'SearchQuery'])
        ;

        $userCollection = $this->createMock(UserCollection::class);

        $this->gitHubApi
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(function($searchQuery) use ($userCollection) {
                $this->assertSame('SearchQuery', $searchQuery);

                return $userCollection;
            })
        ;

        $this->response
            ->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {
                $this->assertArrayHasKey('content', json_decode($content, true));
            })
        ;

        $controller = new Search($this->response);

        $this->assertSame($this->response, $controller->render(
            $this->template,
            $this->feedStorage,
            $this->request,
            $searchForm,
            $this->createForm,
            $this->gateKeeper,
            $this->gitHubApi,
            '12'
        ));
    }

    public function render(
        Html $template,
        Feed $storage,
        Request $request,
        Form $form,
        CreateForm $createForm,
        GateKeeper $gateKeeper,
        Storage $gitHubStorage,
        string $id
    ): Response {
        $users = $gitHubStorage
            ->search($form['query']->getValue())
            ->filter(static function(User $user) use ($feed) {
                return !$feed->hasUserAccess($user);
            })
        ;

        $this->response->setContent(json_encode([
            'content' => $template->render('/feed/administrator/search-result-modal.phtml', [
                'searchForm' => $form,
                'createForm' => $createForm,
                'users'      => $users,
                'feed'       => $feed,
            ]),
        ]));

        return $this->response;
    }
}
