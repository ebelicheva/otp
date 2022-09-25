<?php

declare(strict_types=1);

namespace Core\Db\Contracts;

interface IdAwareInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int|null $id
     * @return IdAwareInterface
     */
    public function setId(?int $id): IdAwareInterface;
}
