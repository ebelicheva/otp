<?php

declare(strict_types=1);

namespace Core\Request\Validators;

use Core\Db\Table;
use Core\Exceptions\CoreException;

class UniqueValidator extends BaseValidator
{
    /**
     * @param string $tableClass
     * @param string $field
     * @throws CoreException
     */
    public function __construct(protected string $tableClass, protected string $field)
    {
        if (!is_subclass_of($this->tableClass, Table::class)) {
            throw new CoreException('Table class needs to be defined here');
        }
    }

    /**
     * @param $value
     * @return bool
     * @throws CoreException
     */
    public function validate($value): bool
    {
        if (!is_scalar($value)) {
            $this->addError('Value is not scalar');
            return false;
        }

        $tableClass = $this->tableClass;

        $existing = $tableClass::getInstance()->findBy([$this->field => $value]);

        if ($existing) {
            $this->addError(sprintf('This %s is already taken', $this->field));
            return false;
        }

        return true;
    }
}
