<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Form;

use CodeCollab\Form\BaseForm;

abstract class Form extends BaseForm
{
    public function toArray(): array
    {
        $form = [
            'validated' => $this->validated,
            'valid'     => $this->valid,
            'fields'    => [],
        ];

        foreach ($this->fieldSet as $field) {
            $form['fields'][$field->getName()] = [
                'value'   => $field->getValue(),
                'isValid' => $field->isValid(),
                'error'   => $field->isValid() ? null : $field->getErrorType(),
            ];
        }

        return $form;
    }
}
