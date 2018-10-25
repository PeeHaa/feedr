<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Form\Repository;

use CodeCollab\Form\Field\Csrf as CsrfField;
use CodeCollab\Form\Field\Text as TextField;
use CodeCollab\Form\Validation\Match as MatchValidator;
use CodeCollab\Form\Validation\Required as RequiredValidator;
use PeeHaa\AwesomeFeed\Form\Form;

class Search extends Form
{
    protected function setupFields()
    {
        $this->addField(new CsrfField('csrfToken', [
            new RequiredValidator(),
            new MatchValidator(base64_encode($this->csrfToken->get())),
        ], base64_encode($this->csrfToken->get())));

        $this->addField(new TextField('query', [
            new RequiredValidator(),
        ]));
    }
}
