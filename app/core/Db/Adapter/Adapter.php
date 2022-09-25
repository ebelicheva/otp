<?php

declare(strict_types=1);

namespace Core\Db\Adapter;

interface Adapter
{
    public function connect();

    public function disconnect();

    public function beginTransaction();

    public function commit();

    public function rollBack();

    public function fetchAll(string $query);

    public function fetchRow(string $query);

    public function quoteIdentifier(string $identifier);

    public function getFields(string $table);

    public function execute(string $query, array $bindValues = []);

    public function getLastInsertId();
}
