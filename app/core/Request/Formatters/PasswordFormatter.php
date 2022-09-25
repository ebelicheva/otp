<?php

declare(strict_types=1);

namespace Core\Request\Formatters;

class PasswordFormatter implements Formatter
{
    /**
     * @param $value
     * @return string|null
     */
    public function format($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return password_hash($value, PASSWORD_ARGON2ID);
    }
}
