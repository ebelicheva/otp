<?php

declare(strict_types=1);

namespace Core\Request\Validators;

class RequiredValidator extends BaseValidator
{
    public function validate($value): bool
    {
        if (!$value) {
            $this->addError('This value should not be empty');
            return false;
        }

        return true;
    }
}
