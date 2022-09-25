<?php

declare(strict_types=1);

namespace Api\Db\Entities;

use Core\Db\Contracts\IdAwareInterface;
use Core\Db\Contracts\IdAwareTrait;
use Core\Db\Contracts\TimestampsAwareInterface;
use Core\Db\Contracts\TimestampsAwareTrait;
use Core\Db\Entity;

/**
 *
 */
final class Message extends Entity implements IdAwareInterface, TimestampsAwareInterface
{
    use IdAwareTrait;
    use TimestampsAwareTrait;

    protected ?int $userId = null;
    protected ?string $message = null;

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     * @return Message
     */
    public function setUserId(?int $userId): Message
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return Message
     */
    public function setMessage(?string $message): Message
    {
        $this->message = $message;
        return $this;
    }
}
