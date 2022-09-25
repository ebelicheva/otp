<?php

declare(strict_types=1);

namespace Core\Response;

interface Response
{
    public function setStatusCode(int $statusCode): Response;

    public function setData(array $data = []): Response;

    public function getContent(): ?string;

    public function send(): void;
}
