<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Form;

use CodeCollab\CsrfToken\Token;
use CodeCollab\Form\Field\Text as TextField;
use CodeCollab\Form\Validation\Required as RequiredValidator;
use CodeCollab\Http\Request\Request;
use PeeHaa\AwesomeFeed\Form\Form;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /** @var MockObject|Token */
    private $csrfToken;

    public function setUp()
    {
        $this->csrfToken = $this->createMock(Token::class);
    }

    public function testToArrayWhenNotValidated()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
            }
        };

        $this->assertFalse($form->toArray()['validated']);
    }

    public function testToArrayWhenValidated()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
            }
        };

        $form->validate();

        $this->assertTrue($form->toArray()['validated']);
    }

    public function testToArrayWhenValid()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
            }
        };

        $form->validate();

        $this->assertTrue($form->toArray()['valid']);
    }

    public function testToArrayWhenNotValid()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
                $this->addField(new TextField('test', [
                    new RequiredValidator(),
                ]));
            }
        };

        $form->validate();

        $this->assertFalse($form->toArray()['valid']);
    }

    public function testToArrayWithValidField()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
                $this->addField(new TextField('test', [
                    new RequiredValidator(),
                ]));
            }
        };

        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $request
            ->method('postArray')
            ->willReturn([
                'test' => 'data',
            ])
        ;

        $form->bindRequest($request);
        $form->validate();

        $this->assertSame($form->toArray(), [
            'validated' => true,
            'valid'     => true,
            'fields'    => [
                'test' => [
                    'value'   => 'data',
                    'isValid' => true,
                    'error'   => null,
                ],
            ],
        ]);
    }

    public function testToArrayWithInvalidField()
    {
        $form = new class($this->csrfToken) extends Form
        {
            public function __construct(Token $csrfToken)
            {
                parent::__construct($csrfToken);
            }

            protected function setupFields()
            {
                $this->addField(new TextField('test', [
                    new RequiredValidator(),
                ]));
            }
        };

        $form->validate();

        $this->assertSame($form->toArray(), [
            'validated' => true,
            'valid'     => false,
            'fields'    => [
                'test' => [
                    'value'   => '',
                    'isValid' => false,
                    'error'   => 'required',
                ],
            ],
        ]);
    }
}
