<?php

declare(strict_types=1);

namespace Api\Services\SmsProvider;

use Api\Db\Entities\User;

interface SmsProviderService
{
    public function send(User $user, string $message): bool;
}
