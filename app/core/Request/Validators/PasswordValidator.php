<?php

declare(strict_types=1);

namespace Core\Request\Validators;

class PasswordValidator extends BaseValidator
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

        if (strlen($value) < 8) {
            $this->addError('Password too short');
            return false;
        }

        //TODO: other validations - numbers, special chars, capital letters, etc

        return true;
    }
}
