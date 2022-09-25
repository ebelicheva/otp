<?php

declare(strict_types=1);

namespace Core\Request;

interface Request
{
    public function setData(array $data): Request;

    public function setHeaders(array $data): Request;

    public function getData(): array;

    public function getSanitizedData(): array;

    public function validate(): array;
}
