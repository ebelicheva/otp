<?php

declare(strict_types=1);

namespace Core\Controllers;

use Core\Response\JsonResponse;

/**
 *
 */
abstract class BaseApiController
{
    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function sendResponse(array $data = [], int $statusCode = 200, array $headers = []): JsonResponse
    {
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode($statusCode);
        $response->setHeaders($headers);

        return $response;
    }
}
