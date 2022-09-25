<?php

declare(strict_types=1);

namespace Core\Db;

use Core\Contracts\Singleton;
use Core\Contracts\SingletonTrait;
use Core\Db\Adapter\Adapter;
use Core\Db\Adapter\PdoMysql;

class Connection implements Singleton
{
    use SingletonTrait;

    protected ?Adapter $adapter = null;

    /**
     * @param array $options
     * @return Connection
     */
    public function connect(array $options): Connection
    {
        $adapter = $options['adapter'] ?? PdoMysql::class;

        if (!class_exists($adapter)) {
            throw new \InvalidArgumentException('DB adapter ' . $adapter . ' not found');
        }

        $this->adapter = new $adapter($options);

        $this->adapter->connect();

        return $this;
    }

    /**
     * @return Connection
     */
    public function disconnect(): Connection
    {
        $this->adapter->disconnect();
        return $this;
    }

    /**
     * @return Adapter|null
     */
    public function getAdapter(): ?Adapter
    {
        return $this->adapter;
    }
}
