<?php

declare(strict_types=1);

namespace Core\Db\Adapter;

use RuntimeException;

final class PdoMysql extends BaseAdapter
{
    private ?\PDO $connection;

    /**
     * @return $this
     */
    public function connect(): PdoMysql
    {
        $driverOptions = [];

        if (!empty($this->options['charset'])) {
            $charsetQuery = "SET NAMES {$this->options['charset']}";
            $driverOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = $charsetQuery;
        }

        $this->connection = new \PDO(
            sprintf(
                'mysql:dbname=%s;host=%s',
                $this->options['dbname'],
                $this->options['host']
            ),
            $this->options['username'],
            $this->options['password'],
            $driverOptions
        );

        return $this;
    }

    public function disconnect(): PdoMysql
    {
        $this->connection = null;

        return $this;
    }

    public function beginTransaction(): PdoMysql
    {
        $this->connection->beginTransaction();

        return $this;
    }

    public function commit(): PdoMysql
    {
        $this->connection->commit();

        return $this;
    }

    public function rollBack(): PdoMysql
    {
        $this->connection->rollBack();

        return $this;
    }

    /**
     * @param string $query
     * @param array $bindValues
     * @return array
     * @throws RuntimeException
     */
    public function fetchRow(string $query, array $bindValues = []): array
    {
        $statement = $this->execute($query, $bindValues);

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $bindValues
     * @return \PDOStatement
     */
    public function execute(string $query, array $bindValues = []): \PDOStatement
    {
        $statement = $this->connection->prepare($query);

        if ($bindValues) {
            foreach ($bindValues as $param => $value) {
                $statement->bindValue($param, $value);
            }
        }

        $statement->execute();

        $errorInfo = $statement->errorInfo();

        //error code is set
        if ($errorInfo[0] !== '00000') {
            throw new RuntimeException($errorInfo[2]);
        }

        return $statement;
    }

    /**
     * @return string|int
     */
    public function getLastInsertId(): string|int
    {
        return $this->connection->lastInsertId();
    }

    /**
     * @param string $table
     * @return array
     */
    public function getFields(string $table): array
    {
        $query = 'SHOW COLUMNS FROM ' . $this->quoteIdentifier($table);

        $fields = $this->fetchAll($query, [], \PDO::FETCH_OBJ);

        $result = [];
        if ($fields) {
            foreach ($fields as $field) {
                $result[$field->Field] = $field->Field;
            }
        }

        return $result;
    }

    /**
     * @param string|int $identifier
     * @return string
     */
    public function quoteIdentifier(string|int $identifier): string
    {
        return "`" . $identifier . "`";
    }

    /**
     * @param string $query
     * @param array $bindValues
     * @param int|null $fetchType
     * @return array|false
     * @throws RuntimeException
     */
    public function fetchAll(string $query, array $bindValues = [], int $fetchType = null): array|false
    {
        $statement = $this->execute($query, $bindValues);

        if (!$fetchType) {
            $fetchType = \PDO::FETCH_OBJ;
        }

        return $statement->fetchAll($fetchType);
    }
}
