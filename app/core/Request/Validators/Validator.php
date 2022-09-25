<?php

declare(strict_types=1);

namespace Core\Request\Validators;

interface Validator
{
    public function validate($value): bool;

    public function getErrors(): array;
}
