<?php

declare(strict_types=1);

namespace Core\Db\Contracts;

trait TimestampsAwareTrait
{
    protected ?\DateTime $createdAt = null;
    protected ?\DateTime $updatedAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @throws \Exception
     */
    public function setCreatedAt(\DateTime|string|null $createdAt): TimestampsAwareInterface
    {
        if (is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }

        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime|string|null $updatedAt): TimestampsAwareInterface
    {
        if (is_string($updatedAt)) {
            $updatedAt = new \DateTime($updatedAt);
        }

        $this->updatedAt = $updatedAt;

        return $this;
    }
}
