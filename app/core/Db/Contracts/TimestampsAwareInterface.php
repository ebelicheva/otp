<?php

declare(strict_types=1);

namespace Core\Db\Contracts;

interface TimestampsAwareInterface
{
    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param string|\DateTime|null $createdAt
     * @return TimestampsAwareInterface
     */
    public function setCreatedAt(string|\DateTime|null $createdAt): TimestampsAwareInterface;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * @param string|\DateTime|null $updatedAt
     * @return TimestampsAwareInterface
     */
    public function setUpdatedAt(string|\DateTime|null $updatedAt): TimestampsAwareInterface;
}
