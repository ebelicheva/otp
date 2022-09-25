<?php

declare(strict_types=1);

namespace Core\Auth\Requests;

use Core\Auth\Authenticatable;
use Core\Auth\Services\JwtTokenService;
use Core\Exceptions\CoreException;
use Core\Exceptions\UnauthorizedHttpException;
use Core\ServiceProvider;

/**
 *
 */
trait JwtAuthenticationRequest
{
    protected ?Authenticatable $user = null;

    public function getUser(): ?Authenticatable
    {
        if (!$this->user) {
            $this->authorize();
        }

        return $this->user;
    }

    /**
     * @return bool
     * @throws CoreException
     * @throws UnauthorizedHttpException
     * @throws \ReflectionException
     */
    public function authorize(): bool
    {
        $authHeader = $this->getHeaders()['Authorization'] ?? null;

        if (!$authHeader) {
            return false;
        }

        $token = preg_replace('/^Bearer\s+/i', '', $authHeader);

        /**
         * @var JwtTokenService $jwtTokenService
         */
        $jwtTokenService = ServiceProvider::getInstance()->getServiceInstance(JwtTokenService::class);

        $this->user = $jwtTokenService->fromToken($token);

        return (bool)$this->user;
    }
}
