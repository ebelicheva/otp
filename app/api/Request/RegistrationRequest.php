<?php

declare(strict_types=1);

namespace Api\Request;

use Api\Db\Tables\Users;
use Core\Request\BaseRequest;
use Core\Request\Formatters\PasswordFormatter;
use Core\Request\Formatters\PhoneFormatter;
use Core\Request\Validators\EmailValidator;
use Core\Request\Validators\PasswordValidator;
use Core\Request\Validators\PhoneValidator;
use Core\Request\Validators\RequiredValidator;
use Core\Request\Validators\UniqueValidator;

/**
 *
 */
class RegistrationRequest extends BaseRequest
{
    public function getRules(): array
    {
        return [
            'email'    => [
                new RequiredValidator(),
                new EmailValidator(),
                new UniqueValidator(Users::class, 'email')
            ],
            'password' => [new RequiredValidator(), new PasswordValidator()],
            'phone'    => [new RequiredValidator(), new PhoneValidator()]
        ];
    }

    public function getFormatters(): array
    {
        return [
            'password' => [new PasswordFormatter()],
            'phone'    => [new PhoneFormatter()]
        ];
    }
}
