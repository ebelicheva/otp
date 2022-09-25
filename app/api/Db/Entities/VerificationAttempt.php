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
final class VerificationAttempt extends Entity implements IdAwareInterface, TimestampsAwareInterface
{
    use IdAwareTrait;
    use TimestampsAwareTrait;

    protected ?int $userId = null;
    protected string|null $code = null;

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     * @return VerificationAttempt
     */
    public function setUserId(?int $userId): VerificationAttempt
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return VerificationAttempt
     */
    public function setCode(?string $code): VerificationAttempt
    {
        $this->code = $code;
        return $this;
    }
}
