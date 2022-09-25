<?php

namespace Core\Controllers\Attributes;

use Attribute;
use Core\Request\BaseRequest;
use Core\Request\Request;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    protected string $controller;
    protected string $action;
    protected string $requestParamName = 'request';

    public function __construct(
        protected string $name,
        protected string $path,
        protected array $methods = [],
    ) {
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return array_map('strtolower', $this->methods);
    }

    /**
     * @return Request
     * @throws \ReflectionException
     */
    public function getRouteRequest(): Request
    {
        if (!$this->getController() || !$this->getAction()) {
            return new BaseRequest();
        }

        $reflectionMethod = new \ReflectionMethod($this->getController(), $this->getAction());

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterType = $parameter->getType()?->getName();

            if (!$parameterType) {
                continue;
            }

            if (is_subclass_of($parameterType, Request::class)) {
                $this->requestParamName = $parameter->getName();
                return new $parameterType();
            }
        }

        return new BaseRequest();
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRequestParamName(): string
    {
        return $this->requestParamName;
    }
}
