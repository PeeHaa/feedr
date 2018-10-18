<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Form\Authentication;

use CodeCollab\Form\BaseForm;
use CodeCollab\Form\Field\Csrf as CsrfField;
use CodeCollab\Form\Field\Password as PasswordField;
use CodeCollab\Form\Field\Text as TextField;
use CodeCollab\Form\Validation\Match as MatchValidator;
use CodeCollab\Form\Validation\Required as RequiredValidator;

class NativeLogIn extends BaseForm
{
    protected function setupFields()
    {
        $this->addField(new CsrfField('csrfToken', [
            new RequiredValidator(),
            new MatchValidator(base64_encode($this->csrfToken->get())),
        ], base64_encode($this->csrfToken->get())));

        $this->addField(new TextField('username', [
            new RequiredValidator(),
        ]));

        $this->addField(new PasswordField('password', [
            new RequiredValidator(),
        ]));
    }
}
