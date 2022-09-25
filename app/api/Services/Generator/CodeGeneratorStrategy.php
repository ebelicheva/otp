<?php

declare(strict_types=1);

namespace Api\Services\Generator;

interface CodeGeneratorStrategy
{
    public function generate(): string;
}
