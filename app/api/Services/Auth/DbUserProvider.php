<?php

declare(strict_types=1);

namespace Api\Services\Auth;

use Api\Db\Entities\User;
use Api\Db\Tables\Users;
use Core\Auth\Providers\UserProvider;

class DbUserProvider implements UserProvider
{
    /**
     * @param $id
     * @return User|null
     * @throws \Core\Exceptions\CoreException
     */
    public function getUser($id): ?User
    {
        return Users::getInstance()->find($id);
    }
}
