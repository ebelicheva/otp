<?php

declare(strict_types=1);

namespace Core\Exceptions;

class NotFoundHttpException extends HttpException
{
    protected int $statusCode = 404;
}
