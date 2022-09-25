<?php

declare(strict_types=1);

namespace Api\Db\Tables;

use Api\Db\Entities\Message;
use Core\Db\Table;

/**
 * @method save(Message $entity): Message
 */
final class Messages extends Table
{
    protected string $tableName = 'messages';

    protected string $entityClass = Message::class;
}
