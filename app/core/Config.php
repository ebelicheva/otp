<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\Singleton;
use Core\Contracts\SingletonTrait;
use Core\Exceptions\CoreException;

class Config implements Singleton
{
    use SingletonTrait;

    protected array $data = [];

    /**
     * @param array $data
     * @return $this
     */
    public function setFromArray(array $data = []): Config
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        return $this->data;
    }

    /**
     * @param string $param
     * @return mixed|null
     */
    public function get(string $param)
    {
        return $this->data[$param] ?? null;
    }

    /**
     * @param string $className
     * @return mixed
     * @throws CoreException
     */
    public function getDependencyClass(string $className): string
    {
        $subClass = $this->data['di'][$className] ?? null;

        if (!$subClass) {
            throw new CoreException(sprintf('No implementation of %s defined', $className));
        }

        if (!class_exists($subClass)) {
            throw new CoreException(sprintf('No class %s found', $subClass));
        }

        if (!is_subclass_of($subClass, $className)) {
            throw new CoreException(sprintf('Class %s is not a subclass of %s', $subClass, $className));
        }

        return $subClass;
    }
}
