<?php

declare(strict_types=1);

namespace Core\Db\Contracts;

trait IdAwareTrait
{
    protected ?int $id = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return IdAwareTrait
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
