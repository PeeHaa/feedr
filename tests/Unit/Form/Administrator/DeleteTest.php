<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Form\Administrator;

use CodeCollab\CsrfToken\Token;
use CodeCollab\Http\Request\Request;
use PeeHaa\AwesomeFeed\Form\Administrator\Delete;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
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
            ])
        ;

        $form = new Delete($this->csrfToken);
        
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
            ])
        ;

        $form = new Delete($this->csrfToken);

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
            ])
        ;

        $form = new Delete($this->csrfToken);

        $form->bindRequest($this->request);
        $form->validate();

        $this->assertTrue($form->isValid());
    }
}
