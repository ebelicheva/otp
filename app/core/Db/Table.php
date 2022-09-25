<?php

declare(strict_types=1);

namespace Core\Db;

use Core\Contracts\Singleton;
use Core\Contracts\SingletonTrait;
use Core\Db\Adapter\Adapter;
use Core\Db\Contracts\TimestampsAwareInterface;
use Core\Exceptions\CoreException;

abstract class Table implements Singleton
{
    use SingletonTrait;

    protected string $tableName;

    protected array $primary = ['id'];

    protected string $entityClass = Entity::class;

    protected ?Connection $connection = null;

    protected array $dbFields = [];

    private function __construct()
    {
        if (!$this->entityClass) {
            throw new \InvalidArgumentException(
                sprintf('No entity class specified in %s', get_class($this))
            );
        }

        if (!class_exists($this->entityClass)) {
            throw new \InvalidArgumentException(
                sprintf('Entity class %s does not exist', $this->entityClass)
            );
        }

        $this->setConnection(Connection::getInstance());

        $this->prepareMetadata();
    }

    /**
     * @return void
     * @throws CoreException
     */
    protected function prepareMetadata(): void
    {
        $this->dbFields = $this->getConnectionAdapter()
                               ->getFields($this->tableName) ?? [];
    }

    /**
     * @return Adapter
     * @throws CoreException
     */
    public function getConnectionAdapter(): Adapter
    {
        $adapter = $this->getConnection()->getAdapter();

        if (!$adapter) {
            throw new CoreException('DB connection not initialized yet');
        }

        return $adapter;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection): Table
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     * @param int $limit
     * @return array
     * @throws CoreException
     */
    public function findBy(array $conditions = [], array $orderBy = [], int $limit = 100): array
    {
        $values = [];
        $bindValues = [];

        $adapter = $this->getConnectionAdapter();

        foreach ($conditions as $field => $value) {
            $values[] = $adapter->quoteIdentifier($field) . ' = :' . $field;
            $bindValues[':' . $field] = $value;
        }

        $orders = [];

        foreach ($orderBy as $field => $direction) {
            $direction = strtolower($direction);

            if (is_numeric($field)) {
                $field = $direction;
                $direction = 'asc';
            }

            if (!in_array($direction, ['asc', 'desc'])) {
                throw new CoreException('Invalid query order direction');
            }

            if (!in_array($field, $this->dbFields, true)) {
                throw new CoreException('Invalid query order field');
            }

            $orders[] = $adapter->quoteIdentifier($field) . ' ' . $direction;
        }

        if (!$orders) {
            $orders = [current($this->primary)];
        }

        $sql = 'SELECT * FROM ' . $adapter->quoteIdentifier($this->tableName)
            . ' WHERE ' . implode(' and ', $values)
            . ' ORDER BY ' . implode(', ', $orders)
            . ' LIMIT ' . $limit;

        return $this->fetchAll($sql, $bindValues);
    }

    /**
     * @param string|null $sql
     * @param array $bindValues
     * @return array
     * @throws CoreException
     */
    public function fetchAll(?string $sql = null, array $bindValues = []): array
    {
        if (!$sql) {
            $sql = 'SELECT * FROM ' . $this->tableName;
        }

        $allRows = $this->getConnectionAdapter()->fetchAll($sql, $bindValues) ?? [];

        $entities = [];

        foreach ($allRows as $row) {
            $entity = new $this->entityClass();
            $entity->fromArray((array)$row);

            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * @param Entity $entity
     * @return Entity
     * @throws CoreException
     * @throws \JsonException
     */
    public function save(Entity $entity): Entity
    {
        $data = $entity->toArray(true);

        foreach (array_keys($data) as $key) {
            if (!in_array($key, $this->dbFields, true)) {
                unset($data[$key]);
            }
        }

        if (!$data) {
            return $entity;
        }

        $primary = [];

        foreach ($this->primary as $field) {
            if (!empty($data[$field])) {
                $primary[$field] = $data[$field];
            }
        }

        if ($primary) {
            $entity = $this->find($primary);

            if ($entity) {
                if ($entity instanceof TimestampsAwareInterface) {
                    $data['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
                }

                $this->update($data);
                return $this->find($primary);
            }
        }

        if ($entity instanceof TimestampsAwareInterface) {
            $data['created_at'] = (new \DateTime())->format('Y-m-d H:i:s');
            $data['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        }

        $primary = $this->insert($data);

        return $this->find($primary);
    }

    /**
     * @param array|int|string $id
     * @return Entity|null
     * @throws CoreException
     */
    public function find(array|int|string $id): ?Entity
    {
        if (func_num_args() > 1) {
            $id = func_get_args();
        }

        $id = (array)$id;

        if (count($this->primary) !== count($id)) {
            throw new CoreException('Primary keys count mismatch');
        }

        $values = [];
        $bindValues = [];

        $adapter = $this->getConnectionAdapter();

        foreach ($this->primary as $field) {
            $values[] = $adapter->quoteIdentifier($field) . ' = :' . $field;

            $value = null;

            if (!empty($id[$field])) {
                $value = $id[$field];
            } else {
                $value = array_shift($id);
            }

            $bindValues[':' . $field] = $value;
        }

        $sql = 'SELECT * FROM ' . $adapter->quoteIdentifier($this->tableName)
            . ' WHERE ' . implode(' and ', $values);

        return $this->fetchRow($sql, $bindValues);
    }

    /**
     * @param string $sql
     * @param array $bindValues
     * @return Entity|null
     * @throws CoreException
     */
    public function fetchRow(string $sql, array $bindValues = []): ?Entity
    {
        $row = $this->getConnectionAdapter()->fetchRow($sql, $bindValues);

        if ($row) {
            $entity = new $this->entityClass();
            $entity->fromArray($row);

            return $entity;
        }

        return null;
    }

    /**
     * @param array $data
     * @return void
     * @throws CoreException
     */
    private function update(array $data): void
    {
        $values = [];

        $adapter = $this->getConnectionAdapter();

        $pkData = [];

        $bindValues = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $this->primary, true)) {
                $pkData[] = $adapter->quoteIdentifier($field) . ' = ' . (int)$value;
                continue;
            }

            $quotedField = $adapter->quoteIdentifier($field);
            $field = ':' . $field;
            $values[] = $quotedField . ' = ' . $field;
            $bindValues[$field] = $value;
        }

        if (empty($values)) {
            return;
        }

        $sql = 'UPDATE ' . $adapter->quoteIdentifier($this->tableName)
            . ' SET ' . implode(', ', $values)
            . ' WHERE ' . implode(' and ', $pkData);

        $adapter?->execute($sql, $bindValues);
    }

    /**
     * @param array $data
     * @return int|string|null
     * @throws CoreException
     */
    private function insert(array $data): int|string|null
    {
        $values = [];

        $bindValues = [];

        $adapter = $this->getConnectionAdapter();

        foreach ($data as $field => $value) {
            if (!is_numeric($value) && in_array($field, $this->primary, true)) {
                continue;
            }

            $quotedField = $adapter->quoteIdentifier($field);
            $field = ':' . $field;
            $values[] = $quotedField . ' = ' . $field;
            $bindValues[$field] = $value;
        }

        $sql = 'INSERT INTO ' . $adapter->quoteIdentifier($this->tableName)
            . ' SET ' . implode(', ', $values);

        $adapter->execute($sql, $bindValues);

        return $adapter->getLastInsertId();
    }

    /**
     * @return Entity
     */
    public function fetchNew(): Entity
    {
        return new $this->entityClass();
    }
}
