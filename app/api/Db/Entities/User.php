<?php

declare(strict_types=1);

namespace Api\Db\Entities;

use Core\Auth\Authenticatable;
use Core\Db\Contracts\IdAwareInterface;
use Core\Db\Contracts\IdAwareTrait;
use Core\Db\Contracts\TimestampsAwareInterface;
use Core\Db\Contracts\TimestampsAwareTrait;
use Core\Db\Entity;

final class User extends Entity implements Authenticatable, IdAwareInterface, TimestampsAwareInterface
{
    use IdAwareTrait;
    use TimestampsAwareTrait;

    protected ?string $email = null;
    protected ?string $phone = null;
    protected ?string $password = null;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return User
     */
    public function setPhone(?string $phone): User
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }
}
