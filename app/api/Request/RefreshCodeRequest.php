<?php

declare(strict_types=1);

namespace Api\Request;

use Core\Auth\Requests\JwtAuthenticationRequest;
use Core\Auth\Requests\NeedsAuthentication;
use Core\Request\BaseRequest;

/**
 *
 */
class RefreshCodeRequest extends BaseRequest implements NeedsAuthentication
{
    use JwtAuthenticationRequest;
}
