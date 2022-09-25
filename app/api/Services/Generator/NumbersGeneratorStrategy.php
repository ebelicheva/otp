<?php

declare(strict_types=1);

namespace Api\Services\Generator;

class NumbersGeneratorStrategy implements CodeGeneratorStrategy
{
    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        return (string)random_int(100000, 999999);
    }
}
