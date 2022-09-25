<?php

declare(strict_types=1);

namespace Core\Auth\Providers;

use Core\Auth\Authenticatable;

interface UserProvider
{
    /**
     * @param $id
     * @return Authenticatable|null
     */
    public function getUser($id): ?Authenticatable;
}
