<?php

declare(strict_types=1);

namespace Core\Request\Formatters;

use Core\Request\Validators\PhoneValidator;

class PhoneFormatter implements Formatter
{
    /**
     * @param $value
     * @return string|null
     */
    public function format($value): ?string
    {
        $value = preg_replace('/\D/', '', $value);

        $phoneLength = strlen($value);

        if ($phoneLength === PhoneValidator::PHONE_DIGITS_WITHOUT_LEADING_ZERO) {
            return PhoneValidator::BG_CODE_PREFIX . $value;
        }

        if (
            $phoneLength === PhoneValidator::PHONE_DIGITS_WITH_LEADING_ZERO
            && str_starts_with($value, '0')
        ) {
            return PhoneValidator::BG_CODE_PREFIX . substr($value, 1);
        }

        if (
            $phoneLength === PhoneValidator::PHONE_DIGITS_WITH_LEADING_CODE
            && str_starts_with($value, PhoneValidator::BG_CODE_PREFIX)
        ) {
            return $value;
        }

        return null;
    }
}
