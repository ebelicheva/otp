<?php

declare(strict_types=1);

namespace Core\Exceptions;

class UnauthorizedHttpException extends HttpException
{
    protected int $statusCode = 401;
}
