<?php

declare(strict_types=1);

namespace Core\Exceptions;

class AccessDeniedHttpException extends HttpException
{
    protected int $statusCode = 403;
}
