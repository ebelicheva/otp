<?php

declare(strict_types=1);

namespace Core\Auth\Services;

use Core\Auth\Authenticatable;
use Core\Auth\Providers\UserProvider;
use Core\Config;
use Core\Exceptions\CoreException;
use Core\Exceptions\UnauthorizedHttpException;
use Core\ServiceProvider;

class JwtTokenService
{
    private const CIPHER_ALGO = 'AES-256-CBC';

    /**
     * @param Authenticatable $user
     * @return string
     * @throws CoreException
     * @throws \JsonException
     */
    public function getToken(Authenticatable $user): string
    {
        $tokenData = [
            'id'  => $user->getId(),
            'exp' => time()
        ];

        $config = Config::getInstance()->get('jwt');

        if (empty($config['encryption_key'])) {
            throw new CoreException('No jwt.encryption_key configured');
        }

        $algo = $config['cipher'] ?? self::CIPHER_ALGO;

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));

        return base64_encode($iv) . '.' . openssl_encrypt(
                json_encode($tokenData, JSON_THROW_ON_ERROR),
                $algo,
                $config['encryption_key'],
                0,
                $iv
            );
    }

    /**
     * @param string $token
     * @return Authenticatable
     * @throws UnauthorizedHttpException
     */
    public function fromToken(string $token): Authenticatable
    {
        try {
            @[$iv, $encoded] = explode('.', $token);

            if (!$iv || !$encoded) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            $config = Config::getInstance()->get('jwt');

            if (empty($config['encryption_key'])) {
                throw new CoreException('No jwt.encryption_key configured');
            }

            $algo = $config['cipher'] ?? self::CIPHER_ALGO;

            $data = openssl_decrypt(
                $encoded,
                $algo,
                $config['encryption_key'],
                0,
                base64_decode($iv)
            );

            if (!$data) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            if (!$data || empty($data['id']) || empty($data['exp'])) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            $lifetime = time() - $data['exp'];

            if ($lifetime > ($config['token_lifetime'] ?? 0)) {
                throw new UnauthorizedHttpException('Token expired');
            }

            $user = ServiceProvider::getInstance()->getServiceInstance(UserProvider::class)->getUser($data['id']);

            if (!$user) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            return $user;
        } catch (UnauthorizedHttpException $e) {
            //TODO: Log
            throw $e;
        } catch (\Exception $e) {
            //TODO: Log
            throw new UnauthorizedHttpException('Unauthorized');
        }
    }
}
