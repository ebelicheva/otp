<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Config;
use Core\Controllers\Attributes\Route;
use Core\Exceptions\NotFoundHttpException;

//Better implement IteratorInterface here

class RoutesCollection
{
    private static ?RoutesCollection $instance = null;

    private function __construct()
    {
    }

    /**
     * @param string $path
     * @param string $method
     * @return Route|null
     * @throws \ReflectionException|NotFoundHttpException
     */
    public function find(string $path, string $method): ?Route
    {
        $hasPathMatch = false;

        foreach ($this->all() as $route) {
            if (trim($route->getPath(), '/') !== trim($path, '/')) {
                continue;
            }

            $hasPathMatch = true;

            if (!$route->getMethods() || in_array(strtolower($method), $route->getMethods(), true)) {
                return $route;
            }
        }

        if ($hasPathMatch) {
            throw new NotFoundHttpException(sprintf('Method %s not supported for path %s', $method, $path));
        }

        return null;
    }

    /**
     * This needs to be cached!!!
     * @return Route[]
     * @throws \ReflectionException
     */
    public function all(): array
    {
        $controllers = $this->getControllers();

        $allRoutes = [];
        foreach ($controllers as $controller) {
            $allRoutes = [...$allRoutes, ...$this->getRoutes($controller)];
        }

        return $allRoutes;
    }

    /**
     * @return \Generator
     */
    private function getControllers(): \Generator
    {
        $pathPrefix = Config::getInstance()->get('path') . '/api/Controllers/';

        $controllersPaths = glob($pathPrefix . '*.php');

        foreach ($controllersPaths as $controllerPath) {
            yield '\\Api\\Controllers\\' . str_replace([$pathPrefix, '.php'], '', $controllerPath);
        }
    }

    public static function getInstance(): RoutesCollection
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $controllerName
     * @return array
     * @throws \ReflectionException
     */
    private function getRoutes(string $controllerName): array
    {
        $reflectionClass = new \ReflectionClass($controllerName);

        if ($reflectionClass->isAbstract()) {
            return [];
        }

        $routes = [];
        foreach ($reflectionClass->getMethods() as $method) {
            $attribs = $method->getAttributes(Route::class);

            foreach ($attribs as $attribute) {
                /**
                 * @var Route $route
                 */
                $route = $attribute->newInstance();
                $route->setController($controllerName);
                $route->setAction($method->getName());

                $routes[] = $route;
            }
        }

        return $routes;
    }
}
