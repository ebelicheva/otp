<?php

declare(strict_types=1);

namespace Core\Db;

use Core\Utils;

class Entity
{
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->fromArray((array)$data);
        }
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function fromArray(array $properties): Entity
    {
        foreach ($properties as $property => $value) {
            $method = 'set' . Utils::underscoreToCamelCase($property);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @param bool $encoded
     * @return array
     * @throws \JsonException
     */
    public function toArray(bool $encoded = false): array
    {
        $properties = get_object_vars($this);

        $result = [];

        foreach ($properties as $key => $value) {
            $key = Utils::camelCaseToUnderscore($key);

            if ($encoded && $value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            if ($encoded && $value && !is_scalar($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
