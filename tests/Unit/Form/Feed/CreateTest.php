<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Form\Feed;

use CodeCollab\CsrfToken\Token;
use CodeCollab\Http\Request\Request;
use PeeHaa\AwesomeFeed\Form\Feed\Create;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    /** @var MockObject|Token */
    private $csrfToken;

    /** @var MockObject|Request */
    private $request;

    public function setUp()
    {
        $this->csrfToken = $this->createMock(Token::class);
        $this->request   = $this->createMock(Request::class);
    }

    public function testRequiredCsrfToken()
    {
        $this->csrfToken
            ->method('get')
            ->willReturn('TheToken')
        ;

        $this->request
            ->method('postArray')
            ->willReturn([
                'csrfToken' => '',
                'name'      => 'Test Feed',
            ])
        ;

        $form = new Create($this->csrfToken);
        
        $form->bindRequest($this->request);
        $form->validate();

        $this->assertFalse($form->isValid());
    }

    public function testMismatchingCsrfToken()
    {
        $this->csrfToken
            ->method('get')
            ->willReturn('TheToken')
        ;

        $this->request
            ->method('postArray')
            ->willReturn([
                'csrfToken' => 'TheToken',
                'name'      => 'Test Feed',
            ])
        ;

        $form = new Create($this->csrfToken);

        $form->bindRequest($this->request);
        $form->validate();

        $this->assertFalse($form->isValid());
    }

    public function testRequiredQuery()
    {
        $this->csrfToken
            ->method('get')
            ->willReturn('TheToken')
        ;

        $this->request
            ->method('postArray')
            ->willReturn([
                'csrfToken' => 'VGhlVG9rZW4=s',
                'name'      => '',
            ])
        ;

        $form = new Create($this->csrfToken);

        $form->bindRequest($this->request);
        $form->validate();

        $this->assertFalse($form->isValid());
    }

    public function testValidRequest()
    {
        $this->csrfToken
            ->method('get')
            ->willReturn('TheToken')
        ;

        $this->request
            ->method('postArray')
            ->willReturn([
                'csrfToken' => 'VGhlVG9rZW4=',
                'name'      => 'Test Feed',
            ])
        ;

        $form = new Create($this->csrfToken);

        $form->bindRequest($this->request);
        $form->validate();

        $this->assertTrue($form->isValid());
    }
}
