<?php

declare(strict_types=1);

namespace Core\Contracts;

trait SingletonTrait
{
    protected static array $instances = [];

    private function __construct()
    {
        //nothing here
    }

    public static function getInstance(): self
    {
        $class = static::class;

        if (empty(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }

        return static::$instances[$class];
    }
}
