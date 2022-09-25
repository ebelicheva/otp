<?php

declare(strict_types=1);

namespace Core\Contracts;

interface Singleton
{
    public static function getInstance(): self;
}
