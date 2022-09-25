<?php

declare(strict_types=1);

namespace Core\Request\Validators;

abstract class BaseValidator implements Validator
{
    protected array $errors = [];

    /**
     * @param string $error
     * @return $this
     */
    public function addError(string $error): BaseValidator
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
