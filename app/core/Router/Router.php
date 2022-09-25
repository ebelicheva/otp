<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Controllers\ErrorController;
use Core\Exceptions\NotFoundHttpException;
use Core\Exceptions\UnauthorizedHttpException;
use Core\Exceptions\ValidationException;
use Core\Response\Response;
use Core\ServiceProvider;

class Router
{
    private ?string $requestMethod;

    /**
     * @param string|null $path
     * @param string|null $requestMethod
     * @param array $requestParams
     * @param array $headers
     */
    public function __construct(
        private readonly ?string $path,
        ?string $requestMethod,
        private readonly array $requestParams = [],
        private readonly array $headers = []
    ) {
        $this->requestMethod = strtolower($requestMethod);
    }

    /**
     * @return Response
     */
    public function route(): Response
    {
        try {
            if (!str_starts_with($this->path, '/api/')) {
                throw new NotFoundHttpException('Path not found. This is just an API interface.');
            }

            $route = RoutesCollection::getInstance()->find($this->path, $this->requestMethod);

            if (!$route) {
                throw new NotFoundHttpException('Not found.');
            }

            $request = $route->getRouteRequest();
            $request->setData($this->requestParams);
            $request->setHeaders($this->headers);

            if (!$request->authorize()) {
                throw new UnauthorizedHttpException('Unauthorized.');
            }

            $errors = $request->validate();

            if ($errors) {
                throw (new ValidationException())->setErrors($errors);
            }

            return ServiceProvider::getInstance()->runMethod(
                $route->getController(),
                $route->getAction(),
                [
                    $route->getRequestParamName() => $request
                ]
            );
        } catch (\Exception $exception) {
            $controller = new ErrorController();
            return $controller->error($exception, $this->requestParams);
        }
    }
}
