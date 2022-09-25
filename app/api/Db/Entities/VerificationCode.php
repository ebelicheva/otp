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
final class VerificationCode extends Entity implements IdAwareInterface, TimestampsAwareInterface
{
    use IdAwareTrait;
    use TimestampsAwareTrait;

    protected ?int $userId = null;
    protected ?string $code = null;
    protected ?int $isUsed = 0;
    protected int $attempts = 0;
    protected ?\DateTime $throttledAt = null;

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     * @return VerificationCode
     */
    public function setUserId(?int $userId): VerificationCode
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
     * @return VerificationCode
     */
    public function setCode(?string $code): VerificationCode
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIsUsed(): ?int
    {
        return $this->isUsed;
    }

    /**
     * @param int|null $isUsed
     * @return VerificationCode
     */
    public function setIsUsed(?int $isUsed): VerificationCode
    {
        $this->isUsed = $isUsed;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     * @return VerificationCode
     */
    public function setAttempts(int $attempts): VerificationCode
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * @return void
     */
    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    /**
     * @return \DateTime|null
     */
    public function getThrottledAt(): ?\DateTime
    {
        return $this->throttledAt;
    }

    /**
     * @param \DateTime|string|null $throttledAt
     * @return $this
     * @throws \Exception
     */
    public function setThrottledAt(\DateTime|string|null $throttledAt): VerificationCode
    {
        if (is_string($throttledAt)) {
            $throttledAt = new \DateTime($throttledAt);
        }

        $this->throttledAt = $throttledAt;
        return $this;
    }
}
