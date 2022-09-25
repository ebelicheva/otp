<?php

declare(strict_types=1);

namespace Core\Request\Validators;

class PhoneValidator extends BaseValidator
{
    public const BG_CODE_PREFIX                    = '359';
    public const PHONE_DIGITS_WITHOUT_LEADING_ZERO = 9;
    public const PHONE_DIGITS_WITH_LEADING_ZERO    = 10;
    public const PHONE_DIGITS_WITH_LEADING_CODE    = 12;

    public function validate($value): bool
    {
        if (!is_string($value)) {
            $this->addError('Value is not string');
            return false;
        }

        if (preg_match('/[a-z]/i', $value)) {
            $this->addError('Phones should not contain letters');
            return false;
        }

        //TODO: Some special characters may also be considered invalid

        $value = preg_replace('/\D/', '', $value);

        $phoneLength = strlen($value);

        if ($phoneLength === self::PHONE_DIGITS_WITHOUT_LEADING_ZERO) {
            return true;
        }

        if (
            $phoneLength === self::PHONE_DIGITS_WITH_LEADING_ZERO
            && str_starts_with($value, '0')
        ) {
            return true;
        }

        if (
            $phoneLength === self::PHONE_DIGITS_WITH_LEADING_CODE
            && str_starts_with($value, self::BG_CODE_PREFIX)
        ) {
            return true;
        }

        $this->addError('Invalid phone number');

        return false;
    }
}
