<?php

declare(strict_types=1);

namespace Core\Response;

class JsonResponse extends BaseResponse
{
    /**
     * @return string|null
     * @throws \JsonException
     */
    public function getContent(): ?string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }

    /**
     * @return void
     */
    protected function appendHeaders(): void
    {
        parent::appendHeaders();
        header('Content-Type: application/json; charset=utf-8');
    }
}
