<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Form\Administrator;

use CodeCollab\Form\Field\Csrf as CsrfField;
use CodeCollab\Form\Validation\Match as MatchValidator;
use CodeCollab\Form\Validation\Required as RequiredValidator;
use PeeHaa\AwesomeFeed\Form\Form;

class Create extends Form
{
    protected function setupFields()
    {
        $this->addField(new CsrfField('csrfToken', [
            new RequiredValidator(),
            new MatchValidator(base64_encode($this->csrfToken->get())),
        ], base64_encode($this->csrfToken->get())));
    }
}
