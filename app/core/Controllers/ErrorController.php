<?php

declare(strict_types=1);

namespace Core\Controllers;

use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;
use Core\Response\JsonResponse;

/**
 *
 */
class ErrorController extends BaseApiController
{
    /**
     * @param \Exception $exception
     * @param array $requestData
     * @return JsonResponse
     */
    public function error(\Exception $exception, array $requestData = []): JsonResponse
    {
        $response = new JsonResponse();
        $response->setStatusCode($exception instanceof HttpException ? $exception->getStatusCode() : 400);

        $data = [
            'success' => false,
            //TODO Not all errors should be exposed to browser, additional check should be applied to hide sensitive data
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidationException) {
            $data['errors'] = $exception->getErrors();
        }

        $response->setData($data);

        return $response;
    }
}
