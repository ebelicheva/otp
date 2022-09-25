<?php

declare(strict_types=1);

namespace Core\Exceptions;

/**
 *
 */
class ValidationException extends HttpException
{
    protected int $statusCode = 422;

    protected array $errors = [];

    public function __construct(string $message = 'Validation Error', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors): ValidationException
    {
        $this->errors = $errors;

        return $this;
    }
}
