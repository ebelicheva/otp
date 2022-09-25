<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\Singleton;
use Core\Contracts\SingletonTrait;
use Core\Exceptions\CoreException;
use ReflectionException;

class ServiceProvider implements Singleton
{
    use SingletonTrait;

    /**
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws CoreException
     * @throws ReflectionException
     */
    public function runMethod(string $className, string $methodName, array $parameters = []): mixed
    {
        if (!method_exists($className, $methodName)) {
            throw new CoreException(sprintf('Method %s not found in class %s', $methodName, $className));
        }

        $object = $this->getServiceInstance($className);

        $params = $this->getMethodParameters($this->getDiClassName($className), $methodName, $parameters);

        return $object->$methodName(...$params);
    }

    /**
     * @param string $className
     * @return mixed
     * @throws CoreException
     * @throws ReflectionException
     */
    public function getServiceInstance(string $className): mixed
    {
        $className = $this->getDiClassName($className);

        $reflectionClass = new \ReflectionClass($className);

        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $className();
        }

        $parameters = $this->getMethodParameters($className, '__construct');

        return new $className(...$parameters);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return array
     * @throws CoreException
     * @throws ReflectionException
     */
    private function getMethodParameters(string $className, string $methodName, array $parameters = []): array
    {
        $reflectionMethod = new \ReflectionMethod($className, $methodName);

        foreach ($reflectionMethod->getParameters() as $parameter) {
            //parameter has default value
            if (array_key_exists($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterType = $parameter->getType()?->getName();

            if (!$parameterType && !$parameter->isOptional()) {
                throw new CoreException(sprintf(
                    'Not sure what value parameter %s::%s %s expects',
                    $className,
                    $methodName,
                    $parameter->getName()
                ));
            }

            $parameters[$parameter->getName()] = $this->getServiceInstance($parameterType);
        }

        return $parameters;
    }

    /**
     * @param string $className
     * @return string
     * @throws CoreException
     * @throws ReflectionException
     */
    private function getDiClassName(string $className): string
    {
        if (!is_a($className, $className, true)) {
            throw new CoreException(sprintf('Class %s not found', $className));
        }

        $reflectionClass = new \ReflectionClass($className);

        if ($reflectionClass->isAbstract()) {
            return Config::getInstance()->getDependencyClass($reflectionClass->getName());
        }

        return $className;
    }
}
