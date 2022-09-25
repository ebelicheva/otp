<?php

declare(strict_types=1);

namespace Core\Response;

abstract class BaseResponse implements Response
{
    protected int $statusCode = 200;
    protected array $data = [];
    protected array $headers = [];

    /**
     * @param array $data
     * @return Response
     */
    public function setData(array $data = []): Response
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $headers
     * @return BaseResponse
     */
    public function setHeaders(array $headers): BaseResponse
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getContent();
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->getStatusCode());
        $this->appendHeaders();

        echo $this->getContent();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    protected function appendHeaders(): void
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }
}
