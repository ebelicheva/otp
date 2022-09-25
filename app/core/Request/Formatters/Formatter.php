<?php

declare(strict_types=1);

namespace Core\Request\Formatters;

interface Formatter
{
    public function format($value);
}
