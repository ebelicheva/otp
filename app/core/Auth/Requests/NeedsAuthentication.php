<?php

declare(strict_types=1);

namespace Core\Auth\Requests;

use Core\Auth\Authenticatable;

/**
 *
 */
interface NeedsAuthentication
{
    public function getUser(): ?Authenticatable;

    public function authorize(): bool;
}
