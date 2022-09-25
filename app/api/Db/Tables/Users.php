<?php

declare(strict_types=1);

namespace Api\Db\Tables;

use Api\Db\Entities\User;
use Core\Db\Table;

/**
 * @method save(User $entity): User
 */
final class Users extends Table
{
    protected string $tableName = 'users';

    protected string $entityClass = User::class;
}
