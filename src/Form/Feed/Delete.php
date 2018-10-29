<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Form\Feed;

use CodeCollab\Form\BaseForm;
use CodeCollab\Form\Field\Csrf as CsrfField;
use CodeCollab\Form\Validation\Match as MatchValidator;
use CodeCollab\Form\Validation\Required as RequiredValidator;

class Delete extends BaseForm
{
    protected function setupFields()
    {
        $this->addField(new CsrfField('csrfToken', [
            new RequiredValidator(),
            new MatchValidator(base64_encode($this->csrfToken->get())),
        ], base64_encode($this->csrfToken->get())));
    }
}
