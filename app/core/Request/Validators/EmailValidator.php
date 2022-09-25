<?php

declare(strict_types=1);

namespace Core\Request\Validators;

class EmailValidator extends BaseValidator
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        if (!is_string($value)) {
            $this->addError('Value is not string');
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError('This is not a valid email');
            return false;
        }

        return true;
    }
}
