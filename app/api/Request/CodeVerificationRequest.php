<?php

declare(strict_types=1);

namespace Api\Request;

use Core\Auth\Requests\JwtAuthenticationRequest;
use Core\Auth\Requests\NeedsAuthentication;
use Core\Request\BaseRequest;
use Core\Request\Validators\RequiredValidator;

/**
 *
 */
class CodeVerificationRequest extends BaseRequest implements NeedsAuthentication
{
    use JwtAuthenticationRequest;

    public function getRules(): array
    {
        return [
            'code' => [new RequiredValidator()],
        ];
    }
}
