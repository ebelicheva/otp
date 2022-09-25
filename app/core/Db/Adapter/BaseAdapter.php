<?php

declare(strict_types=1);

namespace Core\Db\Adapter;

abstract class BaseAdapter implements Adapter
{
    protected array $options = [];

    public function __construct(array $options)
    {
        $this->options = $options;
    }
}
